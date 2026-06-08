<?php

namespace App\Http\Controllers;

use App\Events\MessageReceived;
use App\Helpers\Helper;
use App\Models\Message;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MessageController extends Controller
{

    /**
     * メッセージの一覧取得
     * 【注意】認証が通っていない場合もある
     */
    public function show(Request $request)
    {
        // ブロックしているユーザーIDを取得
        $blockIds = [];
        $user = User::where('api_token', $request->input('api_token'))->first();
        if ($user) {
            foreach ($user->blockUsers as $blockUser) {
                $blockIds[] = $blockUser->blocked_id;
            }
        }

        $roomId = $request->input('room_id');
        $messages = Message::with('user:id,image,name,profile', 'image', 'payment')
            ->where('room_id', $roomId)
            ->orderBy('created_at', 'desc')
            ->get();
        foreach ($messages as $message) {
            // ブロックしていた場合はメッセージを表示しない
            if (in_array($message->user->id, $blockIds)) {
                $message->content = 'このメッセージは表示されません。';
                $message->setRelation('image', null);
            }
            $this->decorateMessageResponse($message);
        }
        if (empty($messages)) {
            abort(404);
        }

        return $messages;
    }

    /**
     * メッセージ受信
     */
    public function store(Request $request)
    {
        // 1. バリデーション
        // 排他は data.image 側の prohibits ルールで担保する
        // （Laravel に prohibited_with は存在しない。data.image が存在する場合、
        //   prohibits 列挙のフィールドは「存在しないこと」が要求される）
        $validator = Validator::make($request->all(), [
            'data.room_id'    => 'required|integer|exists:rooms,id',
            'data.content'    => 'required_without:data.image|nullable|string|max:128',
            // product_id は WEB/iOS どちらも数値で送ってくるため型は限定しない
            // 既存実装は controller 内で == の loose 比較で扱う仕様
            'data.product_id' => 'nullable',
            'data.image'      => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:5120',
                'prohibits:data.content,data.product_id',
            ],
        ]);
        if ($validator->fails()) {
            return response(['error' => 'validation_failed', 'details' => $validator->errors()], 422);
        }

        // 認証・ユーザーIDの確定（以降の箇所で同じ $userId を使い回す）
        $isAuthenticated = Auth::guard('api')->check();
        $userId = $isAuthenticated
            ? Auth::guard('api')->id()
            : config('services.guest_user_id');

        // 画像メッセージはログイン必須
        if ($request->hasFile('data.image') && !$isAuthenticated) {
            return response(['error' => 'login_required_for_image'], 403);
        }

        // 2. ストレージ保存（DBトランザクション外、画像メッセージのみ）
        $savedPath = null;
        $imageMeta = null;
        if ($request->hasFile('data.image')) {
            try {
                $file = $request->file('data.image');

                // 実体検証（PHP 標準の getimagesize、JPEG/PNG/WebP 対応）
                $info = @getimagesize($file->getRealPath());
                $typeToExt = [
                    IMAGETYPE_JPEG => 'jpg',
                    IMAGETYPE_PNG  => 'png',
                    IMAGETYPE_WEBP => 'webp',
                ];
                if ($info === false || !isset($typeToExt[$info[2]])) {
                    return response(['error' => 'invalid_image'], 422);
                }
                $ext      = $typeToExt[$info[2]];
                // ファイル名は既存パターン (Api/UserController.php:94 等) と統一して Str::random(32)
                $filename = Str::random(32) . '.' . $ext;
                $savedPath = "message_images/{$userId}/{$filename}";

                // 元バイナリで一旦保存
                Storage::disk('public')->putFileAs(
                    "message_images/{$userId}",
                    $file,
                    $filename
                );

                // 長辺 1280px にリサイズ＆上書き保存（既存パターン Api/UserController.php:96 等と統一）
                // GD で再エンコードされるため EXIF（GPS 含む）はこの時点で除去される（プライバシー保護）
                Helper::resizeImage(
                    storage_path("app/public/{$savedPath}"),
                    1280
                );

                $imageMeta = [
                    'filename' => $filename,
                    // size はリサイズ後の実サイズを取得
                    'size'     => Storage::disk('public')->size($savedPath),
                ];
            } catch (\Throwable $e) {
                if ($savedPath) {
                    Storage::disk('public')->delete($savedPath);
                }
                throw $e;
            }
        }

        // 3. DB トランザクション
        try {
            $message = DB::transaction(function () use ($request, $imageMeta, $userId, $isAuthenticated) {
                $message = new Message();
                $message->user_id = $userId;
                // multipart/form-data では値がすべて文字列扱いになるため、明示的に int キャスト。
                // これを怠るとレスポンス JSON で room_id が "16" のような文字列になり、
                // iOS の Int デコードが失敗する（テキスト送信は JSON ボディなので問題なし）。
                $message->room_id = (int)$request->input('data.room_id');
                $message->content = $request->input('data.content'); // 画像メッセージなら null
                $message->save();

                if ($imageMeta) {
                    $message->image()->create([
                        'user_id'  => $userId,
                        'filename' => $imageMeta['filename'],
                        'size'     => $imageMeta['size'],
                    ]);
                }

                // ログインユーザーは comment_count をインクリメント（テキスト・画像どちらでも）
                if ($isAuthenticated) {
                    $user = User::find($userId);
                    $user->user_data->comment_count++;
                    $user->push();
                }

                // ギフト処理
                // ★スコープ外: ギフト送信の認証要件は既存挙動を厳密に維持する★
                // 既存実装は Auth::guard('api')->id() を直接利用し、ゲスト時は user_id が null
                // → payments.user_id の NOT NULL 制約で DB エラー（500）となり実質ゲスト禁止
                // ここでは $userId 変数（ゲスト時 guest_user_id フォールバック）を使わず
                // Auth::guard('api')->id() を直接呼び出して既存挙動を維持する
                $productId = $request->input('data.product_id');
                if ($productId) {
                    $authUserId = Auth::guard('api')->id();  // 既存挙動維持: ゲスト時は null
                    $payment = new Payment();
                    $payment->user_id = $authUserId;
                    $payment->message_id = $message->id;
                    $payment->product_id = $productId;
                    // price / point の割当（既存 MessageController.php:93-112 のロジックをそのまま移動）
                    if      ($productId == '1') { $payment->price = 10;   $payment->point = 10;   }
                    elseif  ($productId == '2') { $payment->price = 100;  $payment->point = 100;  }
                    elseif  ($productId == '3') { $payment->price = 200;  $payment->point = 200;  }
                    elseif  ($productId == '4') { $payment->price = 500;  $payment->point = 500;  }
                    elseif  ($productId == '5') { $payment->price = 1000; $payment->point = 1000; }
                    elseif  ($productId == '6') { $payment->price = 2000; $payment->point = 2000; }
                    $payment->save();

                    // 送信者ポイント減算
                    $sender = User::find($authUserId);
                    $sender->user_data->point -= $payment->point;
                    $sender->push();
                    // 配信者ポイント加算（room.user_id、これは $authUserId とは別）
                    $room = Room::find($message->room_id);
                    $room->user->user_data->point += $payment->point;
                    $room->user->push();

                    // ★既存挙動維持（落とすと iOS 送信者画面でギフト表示が出ない regression）★
                    // - 既存実装 (旧 MessageController.php:126-127) はレスポンス前に動的プロパティを挿していた
                    // - iOS の LiveRoomViewerView.send/sendGift は POST レスポンスの Message をそのまま
                    //   append() に渡しており、append() は id で dedup するため Socket 経由の
                    //   broadcastWith（gift_amount 入り）で後から上書きすることもできない
                    // - よって POST レスポンス時点で gift_amount / payment_product_id を含めないと、
                    //   送信者本人の iOS 画面で自分のギフトが「通常のテキスト」のまま配信中ずっと残る
                    // - WEB は POST レスポンスを利用していないため影響なし
                    $message->gift_amount = (int)$payment->point;
                    $message->payment_product_id = (string)$payment->product_id;
                }

                return $message;
            });
        } catch (\Throwable $e) {
            // DB トランザクション失敗 → ストレージに保存済みのファイルを削除
            // クリーンアップ自体が失敗しても元の DB 例外 $e を優先送出する
            if ($savedPath) {
                try {
                    $deleted = Storage::disk('public')->delete($savedPath);
                    if (!$deleted) {
                        Log::warning('message image cleanup failed (silent)', [
                            'path'           => $savedPath,
                            'original_error' => $e->getMessage(),
                        ]);
                    }
                } catch (\Throwable $cleanupError) {
                    Log::warning('message image cleanup threw', [
                        'path'           => $savedPath,
                        'cleanup_error'  => $cleanupError->getMessage(),
                        'original_error' => $e->getMessage(),
                    ]);
                }
            }
            throw $e;
        }

        // 4. commit 後に push 発火
        MessageReceived::dispatch($message);

        $message->load('user:id,image,name,profile', 'image', 'payment');
        $this->decorateMessageResponse($message);

        return response($message, 201);
    }

    /**
     * WEB/iOS の各経路で扱う Message payload を揃える。
     */
    private function decorateMessageResponse(Message $message): Message
    {
        if ($message->relationLoaded('user') && $message->user) {
            // これを呼んでおかないとVue側でリレーションしてくれない
            $message->user->user_data;
            $message->user->image_path = $message->user->getImagePath();
            $message->user_name = $message->user->name;
        }

        $payment = $message->relationLoaded('payment')
            ? $message->payment
            : $message->payment()->first();

        if ($payment) {
            $message->payment_product_id = (string)$payment->product_id;
            $message->gift_amount = (int)$payment->point;
        } else {
            $message->payment_product_id = null;
            $message->gift_amount = null;
        }

        return $message;
    }

}
