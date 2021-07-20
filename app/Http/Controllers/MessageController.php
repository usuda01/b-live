<?php

namespace App\Http\Controllers;

use App\Events\MessageReceived;
use App\Models\Message;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
        $messages = Message::with('user:id,image,name,profile')->where('room_id', $roomId)->orderBy('created_at', 'desc')->get();
        foreach ($messages as $message) {
            $message->user->image_path = $message->user->getImagePath();
            // ブロックしていた場合はメッセージを表示しない
            if (in_array($message->user->id, $blockIds)) {
                $message->content = 'このメッセージは表示されません。';
            }
            // 課金メッセージかどうかを判定
            if ($message->payment) {
                $message->payment_product_id = $message->payment->product_id;
            } else {
                $message->payment_product_id = null;
            }
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
        $validator = Validator::make($request->all(), [
            'data.content' => 'required|max:128',
        ]);
        if ($validator->fails()) {
            return false;
        }

        $message = new Message();
        if (Auth::guard('api')->check()) {
            $message->user_id = Auth::guard('api')->id();
        } else {
            $message->user_id = config('services.guest_user_id');
        }
        $productId = $request->input('data.product_id');
        $message->room_id = $request->input('data.room_id');
        $message->content = $request->input('data.content');
        $message->save();

        // ギフトメッセージ有り
        if ($productId) {
            $payment = new Payment();
            $payment->user_id = Auth::guard('api')->id();
            $payment->message_id = $message->id;
            $payment->product_id = $productId;
            if ($productId == '1') {
                $payment->price = 10;
                $payment->point = 10;
            } else if ($productId == '2') {
                $payment->price = 100;
                $payment->point = 100;
            } else if ($productId == '3') {
                $payment->price = 200;
                $payment->point = 200;
            } else if ($productId == '4') {
                $payment->price = 500;
                $payment->point = 500;
            } else if ($productId == '5') {
                $payment->price = 1000;
                $payment->point = 1000;
            } else if ($productId == '6') {
                $payment->price = 2000;
                $payment->point = 2000;
            }
            $payment->save();

            // 加算
            $room = Room::where('id', $message->room_id)->first();
            $roomUser = $room->user;
            $roomUser->user_data->point += $payment->point;
            $roomUser->push();

            // 減算
            $user = User::where('id', Auth::guard('api')->id())->first();
            $user->user_data->point -= $payment->point;
            $user->push();
        }

        // イベントを発火
        event(new MessageReceived($message));

        return response($message, 201);
    }

}

