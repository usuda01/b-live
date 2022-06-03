<?php
namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        ]);
    }

    // for Xcode
    // webview
    public function message($roomId) {
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
        return view('room.message', [
            'room' => $room,
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
            'room' => $room,
            'user' => $user,
        ]);
    }

    public function stream($roomId) {
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

        return view('room.stream', [
            'room' => $room,
            'user' => $user,
        ]);
    }

    public function search(Request $request) {
        $keyword = $request->input('q');
        if (!$keyword) {
            return redirect('/');
        }

        $rooms = Room::leftJoin('users as joinUsers', 'joinUsers.id', '=', 'rooms.user_id')
            ->leftJoin('games as joinGames', 'joinGames.id', '=', 'rooms.game_id')
            ->select('rooms.*')
            ->where(function($query) use($keyword) {
                $query->orWhere('rooms.status', '=', '1')
                    ->orWhere('rooms.status', '=', '2');
                })
            ->where(function($query) use($keyword) {
                $query->orWhere('rooms.name', 'like', '%' . $keyword . '%')
                    ->orWhere('joinGames.name', 'like', '%' . $keyword . '%')
                    ->orWhere('joinUsers.name', 'like', '%' . $keyword . '%');
            })
            ->orderBy('rooms.published_at', 'desc')->get();

        return view('room.search', [
            'rooms' => $rooms,
        ]);
    }
}
