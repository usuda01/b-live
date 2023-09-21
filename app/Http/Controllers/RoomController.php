<?php
namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Room;
use App\Models\User;
use App\Models\UserViewTime;
use App\Models\UserViewTimeLog;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{

/*
    // 使ってない
    public function index(Request $request) {
        $data = Room::where('status', 1)->orderBy('published_at', 'desc')->get();
        foreach ($data as $value) {
            $value->image_url = url($value->getImagePath());
        }
        return response()->json([
            'rooms' => $data
        ]);
    }
*/

    public function countViews(Request $request) {
        $roomId = $request->input('data.room_id');
        $ipAddress = $request->ip();

        // $roomId が取得できなかった場合は0を返す
        if (!$roomId) {
            return response()->json([
                'views' => 0,
            ]);
        }

        // IPアドレス が取得できなかった場合はカウントしない
        if (!$ipAddress) {
            // レコード数を取得
            $views = Log::where('room_id', $roomId)->count();
            return response()->json([
                'views' => $views,
            ]);
        }

        // 重複チェック
        $log = Log::where('room_id', $roomId)
            ->where('ip_address', $ipAddress)
            ->where('created_at', '>', date('Y-m-d H:i:s', strtotime('-30 second')))
            ->first();
        if (!$log) {
            $log = Log::create([
                'room_id' => $roomId,
                'ip_address' => $ipAddress,
            ]);
        }

        // 60秒以前のレコードの削除
        Log::where('created_at', '<=', date('Y-m-d H:i:s', strtotime('-60 second')))->delete();

        // 同時視聴者数を取得
        $views = Log::where('room_id', $roomId)
            ->where('created_at', '>', date('Y-m-d H:i:s', strtotime('-30 second')))
            ->count();

        $room = Room::where('id', $roomId)->first();
        // ライブ中のみの処理
        if ($room->status == '1') {
            // 最大同時視聴者数の更新
            if ($room->max_view < $views) {
                $room->max_view = $views;
                $room->save();
            }

            // 動画ランクの更新
            $room->updateRank();
        }

        return response()->json([
            'views' => $views,
            'status' => $room->status,
        ]);
    }

    /*
     * 視聴時間の記録
     */
    public function storeViewTime(Request $request) {
        $viewerUserId = $request->input('viewer_user_id');
        $viewedUserId = $request->input('viewed_user_id');
        $duration = $request->input('duration');

        /*
         * 重複カウントを防ぐため、
         * durationの時間内にLogがあったら処理を中断する
         */
        $userViewTimeLog = UserViewTimeLog::where('viewer_user_id', $viewerUserId)
            ->where('created_at', '>', date('Y-m-d H:i:s', strtotime('-' . ($duration - 2) . 'second')))
            ->first();
        if ($userViewTimeLog) {
            return;
        }

        $userViewTimeLog = new UserViewTimeLog();
        $userViewTimeLog->viewer_user_id = $viewerUserId;
        $userViewTimeLog->viewed_user_id = $viewedUserId;
        $userViewTimeLog->view_time = Helper::secToStr($duration);
        $userViewTimeLog->save();

        // insert or update
        $userViewTime = UserViewTime::where('viewer_user_id', $viewerUserId)
            ->where('viewed_user_id', $viewedUserId)
            ->first();
        if (!$userViewTime) {
            // insert
            $userViewTime = new UserViewTime();
            $userViewTime->viewer_user_id = $viewerUserId;
            $userViewTime->viewed_user_id = $viewedUserId;
            $userViewTime->view_time = Helper::secToStr($duration);
            $userViewTime->save();
        } else {
            // update
            $timeToAdd = Helper::secToStr($duration);
            UserViewTime::where('id', $userViewTime->id)
                ->update([
                    'view_time' => DB::raw("SEC_TO_TIME(TIME_TO_SEC(view_time) + TIME_TO_SEC('$timeToAdd'))")
                ]);
        }
    }

    // for Xcode
    // webview
    public function message($roomId) {
        $isApp = true; // アプリからの接続かどうか

        $room = Room::with('user')->where('id', $roomId)->first();
        if (!$room) {
            abort(404);
        }
        $room->user->image_path = $room->user->getImagePath();
        // これを呼んでおかないとVue側でリレーションしてくれない
        $room->user->user_data;
        $room->game;
        $user = Auth::user();
        if ($user) {
            $user->image_path = $user->getImagePath();
            $user->block_users = $user->BlockUsers;
            $user->point = $user->user_data->point;
        }
        // 2週間以前の動画は期限切れとする
        $isExpired = false;
        if ($room->published_at < date('Y-m-d H:i:s', strtotime('-14 day'))) {
            $isExpired = true;
        }
        $room->is_expired = $isExpired;

        // リスナー
        $listeners = [];
        $userViewTimes = UserViewTime::where('viewed_user_id', $room->user->id)
            ->orderBy('view_time', 'desc')
            ->get();
        foreach ($userViewTimes as $userViewTime) {
            $listener = User::where('id', $userViewTime->viewer_user_id)->first();
            $listeners[] = [
                'id' => $listener->id,
                'user_name' => $listener->name,
                'user_image_path' => $listener->getImagePath(),
                'view_time' => $userViewTime->view_time,
            ];
        }
        $listeners = json_encode($listeners);

        return view('room.message', [
            'isApp' => $isApp,
            'room' => $room,
            'listeners' => $listeners,
            'user' => $user,
        ]);
    }

    // for Xcode
    public function getInfo($roomId) {
        $room = Room::where('id', $roomId)->first();
        if (!$room) {
            abort(404);
        }
        if ($room->status == '1') {
            //$videoUrl = config('services.rtmp_url') . '/' . $room->stream_key;
            $videoUrl = $room->wowza->hls_url;
        } else {
            $videoUrl = 'https://b-live-bucket.s3.us-east-2.amazonaws.com/' . $room->stream_key . '.mp4';
        }
        // 2週間以前の動画は期限切れとする
        $isExpired = false;
        if ($room->published_at < date('Y-m-d H:i:s', strtotime('-14 day'))) {
            $isExpired = true;
        }
        $is_expired = $isExpired;
        $imageUrl = url($room->getImagePath());
        return response()->json([
            'is_expired' => $isExpired,
            'image_url' => $imageUrl,
            'room_id' => $room->id,
            'video_url' => $videoUrl,
            'status' => $room->status
        ]);
    }

    // for コメントビューワー
    public function messageViewer($roomId) {
        $isApp = false; // アプリからの接続かどうか

        $room = Room::with('user')->where('id', $roomId)->first();
        if (!$room) {
            abort(404);
        }
        $room->user->image_path = $room->user->getImagePath();
        // これを呼んでおかないとVue側でリレーションしてくれない
        $room->user->user_data;
        $user = Auth::user();
        if ($user) {
            $user->image_path = $user->getImagePath();
            $user->block_users = $user->BlockUsers;
            $user->point = $user->user_data->point;
        }
        // 2週間以前の動画は期限切れとする
        $isExpired = false;
        if ($room->published_at < date('Y-m-d H:i:s', strtotime('-14 day'))) {
            $isExpired = true;
        }
        $room->is_expired = $isExpired;
        return view('room.message_viewer', [
            'isApp' => $isApp,
            'room' => $room,
            'user' => $user,
        ]);
    }

    public function stream($roomId) {
        $isApp = false; // アプリからの接続かどうか

        $room = Room::with('user')->where('id', $roomId)->first();
        if (!$room) {
            abort(404);
        }
        $room->image_path = $room->getImagePath();
        $room->user->image_path = $room->user->getImagePath();
        // これを呼んでおかないとVue側でリレーションしてくれない
        $room->user->user_data;
        $room->game;
        $user = Auth::user();
        if ($user) {
            $user->image_path = $user->getImagePath();
            $user->block_users = $user->BlockUsers;
            $user->point = $user->user_data->point;
        }
        // 2週間以前の動画は期限切れとする
        $isExpired = false;
        if ($room->published_at < date('Y-m-d H:i:s', strtotime('-14 day'))) {
            $isExpired = true;
        }
        $room->is_expired = $isExpired;

        // これを呼んでおかないとVue側でリレーションしてくれない
        $room->wowza;

        // リスナー
        $listeners = [];
        $userViewTimes = UserViewTime::where('viewed_user_id', $room->user->id)
            ->orderBy('view_time', 'desc')
            ->get();
        foreach ($userViewTimes as $userViewTime) {
            $listener = User::where('id', $userViewTime->viewer_user_id)->first();
            $listeners[] = [
                'id' => $listener->id,
                'user_name' => $listener->name,
                'user_image_path' => $listener->getImagePath(),
                'view_time' => $userViewTime->view_time,
            ];
        }
        $listeners = json_encode($listeners);

        return view('room.stream', [
            'isApp' => $isApp,
            'room' => $room,
            'listeners' => $listeners,
            'user' => $user,
        ]);
    }
}
