<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Movie;
use App\Models\PointRequest;
use App\Models\Room;
use App\Models\User;
use App\Models\UserData;
use App\Models\Wowza;
use FFMpeg;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Ncmb\NCMB;
use Ncmb\Push;

class SettingController extends Controller
{

    public function coin() {
        $user = Auth::user();
        return view('setting.coin', [
            'user' => $user
        ]);
    }

    public function coinRequest() {
        $user = Auth::user();
        return view('setting.coin_request', [
            'user' => $user
        ]);
    }

    public function coinRequestPost(Request $request) {
        $user = Auth::user();

        $requestPoint = $request->input('request_point');
        $bankName = $request->input('bank_name');
        $branchName = $request->input('branch_name');
        $accountType = $request->input('account_type');
        $accountNumber = $request->input('account_number');
        $accountName = $request->input('account_name');
        $amount = floor($requestPoint * 0.3);
        $validator = Validator::make($request->all(), [
            'request_point' => 'required|integer',
            'bank_name' => 'required|max:64',
            'branch_name' => 'required|max:64',
            'account_type' => ['required', Rule::in(['1', '2', '3'])],
            'account_number' => 'required|integer',
            'account_name' => 'required|max:64',
        ]);

        $validator->after(function($validator) use($user, $requestPoint) {
            if ($requestPoint > $user->user_data->point || $requestPoint <= 0 || $requestPoint < 1000) {
                $validator->errors()->add('request_point', '申請できるコインが範囲外です');
            }
        });

        if ($validator->fails()) {
            return redirect('/setting/coin-request')
                ->withErrors($validator)
                ->withInput();
        }

        $pointRequest = new PointRequest();
        $pointRequest->user_id = $user->id;
        $pointRequest->request_point = $requestPoint;
        $pointRequest->amount = $amount;
        $pointRequest->bank_name = $bankName;
        $pointRequest->branch_name = $branchName;
        $pointRequest->account_type = $accountType;
        $pointRequest->account_number = $accountNumber;
        $pointRequest->account_name = $accountName;
        $pointRequest->save();
        $user->user_data->point -= $requestPoint;
        $user->push();

        return redirect('setting/coin');
    }

    // クラン
    public function group($groupId = null) {
        /*
         * 新規作成、更新の場合がある
         */
        $user = Auth::user();

        if ($groupId) {
            $group = Group::where('id', $groupId)->first();
        } else {
            $group = new Group();
        }
        return view('setting.group', [
            'group' => $group
        ]);
    }

    public function groupPost(Request $request) {
        /*
         * 新規作成、更新、削除の場合がある
         */
        $user = Auth::user();
        $mode = $request->input('mode');
        $groupId = $request->input('group_id');
        $gameTitle = $request->input('game_title');
        $name = $request->input('name');
        $memberNumber = $request->input('member_number');
        $description = $request->input('description');
        $isPublish = $request->input('is_publish');

        if ($mode == 'delete') {
            $group = Group::where('id', $groupId)->first();
            $group->delete();
            $request->session()->flash('flash_message', 'クランを削除しました');
            return redirect('setting/group-list');
        }

        $validator = Validator::make($request->all(), [
            'game_title' => 'required|max:128',
            'name' => 'required|max:64',
            'member_number' => 'required|integer',
            'description' => 'required|max:1000',
            'is_publish' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return redirect('setting/group/' . $groupId)
                ->withErrors($validator)
                ->withInput();
        }

        if ($mode == 'edit') {
            $group = Group::where('id', $groupId)->first();
            $request->session()->flash('flash_message', 'クラン情報を更新しました');
        } else if ($mode == 'create') {
            $group = new Group();
            $request->session()->flash('flash_message', 'クラン情報を作成しました');
        }

        $originImg = $request->image;
        if ($originImg) {
            if ($originImg->isValid()) {
                Helper::resizeImage($originImg->getPathname(), 1280);
                $filePath = $originImg->store('public/group');
                $group->image = str_replace('public/group/', '', $filePath);
            } else {
                // TODO エラーメッセージを表示する
                //echo $originImg->getErrorMessage();
                //exit();
            }
        }

        $group->user_id = $user->id;
        $group->game_title = $gameTitle;
        $group->name = $name;
        $group->member_number = $memberNumber;
        $group->description = $description;
        $group->is_publish = $isPublish;
        $group->save();

        return redirect('setting/group-list');
    }

    // クラン一覧
    public function groupList() {
        $user = Auth::user();
        $groups = Group::where('user_id', $user->id)->orderBy('id', 'desc')->get();
        return view('setting.group_list', [
            'groups' => $groups
        ]);
    }

    // 動画
    public function movie($movieId = null) {
        /*
         * 新規作成、更新の場合がある
         */
        $user = Auth::user();

        if ($movieId) {
            $movie = Movie::where('id', $movieId)->first();
        } else {
            $movie = new Movie();
        }
        return view('setting.movie', [
            'movie' => $movie
        ]);
    }

    public function moviePost(Request $request) {
        /*
         * 新規作成、更新、削除の場合がある
         * 動画は更新できないようにする
         * アップできる動画数はMAX_MOVIE_UPLOADまで
         */
        $user = Auth::user();
        $mode = $request->input('mode');
        $movieId = $request->input('movie_id');
        $gameId = $request->input('game_id');
        $name = $request->input('name');
        $isPublish = $request->input('is_publish');

        if ($mode == 'create') {
            // 新規作成
            $movieCount = Movie::where('user_id', $user->id)->count();
            if ($movieCount >= config('services.max_movie_upload')) {
                $request->session()->flash('flash_message', 'アップロードできる動画は'.config('services.max_movie_upload').'つまでです');
                return redirect('setting/movie');
            }

            $originMovie = $request->file('movie');
            if (!$originMovie) {
                abort(404);
            }
            $pathInfo = pathinfo($originMovie->getClientOriginalName());
            $fileName = date('Y-m-d-H-i-s') . '.' . $pathInfo['extension'];
            if ($originMovie->isValid()) {
                if (!in_array($pathInfo['extension'], ['mp4', 'MP4', 'MOV'])) {
                    $request->session()->flash('flash_message', 'mp4形式の動画を投稿してください');
                    return redirect('setting/movie');
                }
            } else {
                //echo $originMovie->getErrorMessage();
                abort(404);
            }

            $originMovie->storeAs('public/movies', $fileName);

            // mov→mp4へ変換
            if ($pathInfo['extension'] === 'MOV') {
                $path = storage_path('app/public/movies/');
                // ffmpeg -i xxx.MOV xxx.mp4
                $out = shell_exec('ffmpeg -i '.$path.$fileName.' '.$path.str_replace($pathInfo['extension'], '', $fileName).'mp4');
                Storage::disk('public')->delete('movies/'.$fileName);
                $fileName = str_replace($pathInfo['extension'], 'mp4', $fileName);
            }

            $media = FFMpeg::fromDisk('public')->open('movies/'.$fileName);
            $durationInSeconds = $media->getDurationInSeconds();
            if ($durationInSeconds > config('services.max_movie_upload_seconds')) {
                Storage::disk('public')->delete('movies/'.$fileName);
                $request->session()->flash('flash_message', config('services.max_movie_upload_seconds').'秒以内の動画を投稿してください');
                return redirect('setting/movie');
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:64',
                'is_publish' => 'required|integer',
            ]);
            $movie = new Movie();
            $movie->path = $fileName;
        } else if ($mode == 'edit') {
            // 編集
            $movie = Movie::where('id', $movieId)->first();
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:64',
                'is_publish' => 'required|integer',
            ]);
        } else if ($mode == 'delete') {
            // 削除
            $movie = Movie::where('id', $movieId)->first();
            Storage::disk('public')->delete('movies/'.$movie->path);
            if ($movie->image) {
                Storage::disk('public')->delete('movies/'.$movie->image);
            }
            $movie->movie_goods()->each(function ($movieGoods) {
                $movieGoods->delete();
            });
            $movie->delete();
            $request->session()->flash('flash_message', '動画を削除しました');
            return redirect('setting/movie-list');
        }

        if ($validator->fails()) {
            if ($mode == 'create') {
                Storage::disk('public')->delete('movies/'.$fileName);
            }

            return redirect('setting/movie/' . $movieId)
                ->withErrors($validator)
                ->withInput();
        }

        if ($mode == 'create') {
            $request->session()->flash('flash_message', '動画を投稿しました');
        } else if ($mode == 'edit') {
            $request->session()->flash('flash_message', '動画情報を更新しました');
        }

        $originImg = $request->file('image');
        if ($originImg) {
            if ($originImg->isValid()) {
                Helper::resizeImage($originImg->getPathname(), 1280);
                $filePath = $originImg->store('public/movies');
                $movie->image = str_replace('public/movies/', '', $filePath);
            } else {
                // TODO エラーメッセージを表示する
                //echo $originImg->getErrorMessage();
                //exit();
            }
        }

        $movie->user_id = $user->id;
        $movie->game_id = $gameId;
        $movie->name = $name;
        $movie->is_publish = $isPublish;
        $movie->save();

        return redirect('setting/movie-list');
    }

    // 動画一覧
    public function movieList() {
        $user = Auth::user();
        $movies = Movie::where('user_id', $user->id)->orderBy('id', 'desc')->get();
        return view('setting.movie_list', [
            'movies' => $movies
        ]);
    }

    public function profile() {
        $user = Auth::user();
        return view('setting.profile', [
            'user' => $user
        ]);
    }

    public function profilePost(Request $request) {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:24',
            'email' => 'required|max:64',
            'profile' => 'max:500',
            'twitter_url' => 'max:128',
            'image' => 'image',
        ]);

        if ($validator->fails()) {
            return redirect('/setting/profile')
                ->withErrors($validator)
                ->withInput();
        }

        $originImg = $request->image;
        if ($originImg) {
            if ($originImg->isValid()) {
                Helper::resizeImage($originImg->getPathname(), 1280);
                $filePath = $originImg->store('public/users');
                $user->image = str_replace('public/users/', '', $filePath);
            }
        }
//        $user->fill($request->all())->save();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->profile = $request->input('profile');
        $user->twitter_url = $request->input('twitter_url');
        $user->save();

        if ($request->input('line_disconnect') == '1') {
            $user->user_data->is_line_connected = null;
        }
        $user->user_data->join_ranking = $request->input('join_ranking');
        if ($request->input('line_notice') !== null) {
            $user->user_data->line_notice = $request->input('line_notice');
        }
        $user->user_data->save();
        $request->session()->flash('flash_message', '更新しました');

        return redirect('setting/profile');
    }

    // LINE連携
    public function line(Request $request) {
        return view('setting.line', [
        ]);
    }

    public function lineCallback(Request $request) {
        $accessToken = config('services.line_message.access_token');

        //ユーザーからのメッセージ取得
        $json_string = file_get_contents('php://input');
        $json_object = json_decode($json_string);

        //取得データ
        $replyToken = $json_object->{"events"}[0]->{"replyToken"}; //返信用トークン
        $userId = $json_object->{"events"}[0]->{"source"}->{"userId"}; // ユーザーID
        $messageType = $json_object->{"events"}[0]->{"message"}->{"type"}; //メッセージタイプ
        $messageText = $json_object->{"events"}[0]->{"message"}->{"text"}; //メッセージ内容
        $messageText = preg_replace("/\r\n|\r|\n/", "\n", $messageText); // 改行コードを揃える

        // メッセージタイプが「text」以外のときは何も返さず終了
        if ($messageType != 'text') exit;

        $id = '';
        if (preg_match('/【連携ID】F([0-9]+)6C\n/', $messageText, $matches)) {
            $id = $matches[1];
        } else {
            Helper::sendLineMessages($accessToken, $replyToken, $messageType, "無効なメッセージです");
            return;
        }

        // ユーザーを更新
        $user = User::where('id', $id)->first();

        if (!$user) {
            Helper::sendLineMessages($accessToken, $replyToken, $messageType, "無効な連携IDです");
            return;
        }

        $user->line_id = $userId;
        $user->save();

        $user->user_data->is_line_connected = 1;
        $user->user_data->save();

        $returnMessageText = "LINE連携が完了しました！\n引き続きB-LIVEをお楽しみください！";

        // 返信
        Helper::sendLineMessages($accessToken, $replyToken, $messageType, $returnMessageText);
    }

    public function stream($roomId = null) {
        /*
         * 新規作成、更新の場合がある
         */
        $user = Auth::user();

        // ユーザーのwowzaがある場合はそれを使い、無い場合は新しいwowzaを作成する
        $wowza = Wowza::where('user_id', $user->id)->first();
        if (!$wowza) {
            $streamKey = Str::random(8);
            $wowza = Wowza::create([
                'user_id' => $user->id,
                'server_url' => 'rtmps://5f1ee0e19125e.streamlock.net/blive',
                'stream_key' => $streamKey,
                'hls_url' => 'https://5f1ee0e19125e.streamlock.net/blive/' . $streamKey . '/playlist.m3u8',
                'started_at' => date('Y-m-d H:i:s'),
                'status' => 1,
            ]);
        }

        if (!$roomId) {
            // ライブ配信中にアクセスしたらリダイレクト
            $liveRoom = Room::where('user_id', $user->id)->where('status', 1)->first();
            if ($liveRoom) {
                return redirect('setting/stream/' . $liveRoom->id);
            }

            $room = new Room();
        } else {
            $room = Room::where('id', $roomId)->first();
        }
        $liveRooms = Room::where('status', 1)->get();
        if ($liveRooms->count() >= config('services.max_liver') && $roomId == null) {
            session()->flash('flash_message', '現在配信者数が' . config('services.max_liver') . '人以上います。もう少し待ってから配信してね！');
        }
        return view('setting.stream', [
            'room' => $room,
            'liveRooms' => $liveRooms,
            'wowza' => $wowza
        ]);
    }

    public function streamPost(Request $request) {
        /*
         * 新規作成、更新、終了の場合がある
         */
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:32',
            'description' => 'max:1000',
        ]);

        $mode = $request->input('mode');
        $roomId = $request->input('room_id');
        $gameId = $request->input('game_id');
        $name = $request->input('name');
        $description = $request->input('description');
        $streamAlert = $request->input('stream_alert');
        $status = $request->input('status');
        $wowzaId = $request->input('wowza_id');

        if ($validator->fails()) {
            return redirect('setting/stream')
                ->withErrors($validator)
                ->withInput();
        }

        // updateOrCreateOrEnd
        if ($mode == 'end') {
            $room = Room::where('id', $roomId)->first();
            $request->session()->flash('flash_message', '配信を終了しました');
        } else if ($mode == 'edit') {
            $room = Room::where('id', $roomId)->first();
            $room->status = $status;
            $request->session()->flash('flash_message', '更新しました');
        } else if ($mode == 'create') {
            $userId = $user->id;

            // 重複チェック
            $room = Room::where('user_id', $userId)->where('status', 1)->first();
            if ($room) {
                $request->session()->flash('flash_message', 'ライブ配信を開始しました');
                return redirect('setting/stream/' . $room->id);
            }

            $room = new Room();
            $room->user_id = $userId;
            $room->published_at = date('Y-m-d H:i:s');
            $room->status = 1;
            $room->wowza_id = $wowzaId;

            $wowza = Wowza::where('id', $wowzaId)->first();
            $wowza->started_at = date('Y-m-d H:i:s');
            $wowza->status = 1;
            $wowza->save();

            $request->session()->flash('flash_message', 'ライブ配信を開始しました');
        }

        $originImg = $request->image;
        if ($originImg) {
            if ($originImg->isValid()) {
                Helper::resizeImage($originImg->getPathname(), 1280);
                $filePath = $originImg->store('public/rooms');
                $room->image = str_replace('public/rooms/', '', $filePath);
            } else {
                // TODO エラーメッセージを表示する
                //echo $originImg->getErrorMessage();
                //exit();
            }
        }
        $room->game_id = $gameId;
        $room->name = $name;
        $room->description = $description;
        if ($mode == 'end') {
            $room->finish();
        }
        $room->push();

        if ($mode == 'create') {
            /*
             * このユーザーをフォローしているユーザーに
             * Push通知
             */
            if ($streamAlert == '1') {
                foreach ($room->user->followers as $follower) {
                    if ($follower->followerUser->device_token) {
                        // ncmbmania/php-ncmbがguzzlehttp/guzzleの7系に対応していないためインストールできない
/*
                        NCMB::initialize(config('services.ncmb.applicationkey'), config('services.ncmb.clientkey'));
                        Push::Send(array(
                            'immediateDeliveryFlag' => true,
                            'target' => ['ios'],
                            'title' => $room->user->name . 'さんが配信を開始しました!',
                            'message' => $room->name,
                            'badgeIncrementFlag' => false,
                            'sound' => 'default',
                            'searchCondition' => ['deviceToken' => $follower->followerUser->device_token]
                        ));
*/
                    }

                    // LINE通知
                    if ($follower->followerUser->user_data->is_line_connected == 1) {
                        // 通知設定
                        if ($follower->followerUser->user_data->line_notice == 1) {
                            $lineMessage = "{$follower->followerUser->name}さん\n"
                                . "【{$room->user->name}】さんが配信を開始しました！\n"
                                . $room->name . "\n"
                                . config('app.url').'/room/'.$room->id;
                            Helper::pushLineMessage($follower->followerUser->line_id, $lineMessage);
                        }
                    }
                }
            }
        }

        return redirect('setting/stream/' . $room->id);
    }

    public function archive() {
        $user = Auth::user();
        $rooms = Room::where('user_id', $user->id)->orderBy('id', 'desc')->get();

        $streamKey = '';
        $wowza = Wowza::where('user_id', $user->id)->first();
        if ($wowza) {
            $streamKey = $wowza->stream_key;
        }

        return view('setting.archive', [
            'rooms' => $rooms,
            'streamKey' => $streamKey
        ]);
    }

    public function payment() {
        return view('setting.payment', [
        ]);
    }

    public function paymentConfirm(Request $request) {
        $validatedData = $request->validate([
            'plan1_id' => 'required',
            'email' => 'required|email|max:255',
            'stripeToken' => 'required',
        ]);

        $input = $request->all();

        return view('setting.payment_confirm', [
            'input' => $input,
        ]);
    }

    public function paymentExec(Request $request) {
        $user = Auth::user();

        $action = $request->input('action');

        //フォームから受け取ったactionを除いたinputの値を取得
        $input = $request->except('action');

        //actionの値で分岐
        if ($action == 'back') {
            return redirect('/setting/payment', 302, [], true)
                ->withInput($input);
        }

        $plan1Id = $request->input('plan1_id');
        $email = $request->input('email');
        $token = $request->input('stripeToken');

        \Stripe\Stripe::setApiKey(config('services.stripe.secret_key'));

        /*
         * 課金処理
         */
        // Stripeに顧客情報を作成を作成
        $customer = \Stripe\Customer::create([
            'email' => $email,
            'source' => $token,
            'description' => 'user_id ' . $user->id,
        ]);

        // 定期課金
        $subscription = \Stripe\Subscription::create([
            'customer' => $customer->id,
            'items' => [['plan' => $plan1Id]], // 商品ID
        ]);

        // ユーザーデータを更新
        $user_data = UserData::where('user_id', $user->id)->first();
        $user_data->stripe_id = $customer->id;
        $user_data->stripe_status = 1;
        $user_data->save();

        // メール送信はStripe側で送る

        return redirect('/setting/payment-complete', 302, [], true);
    }

    public function paymentComplete(Request $request) {
        return view('setting.payment_complete', [
        ]);
    }
}

