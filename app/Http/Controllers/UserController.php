<?php
namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use App\Models\UserViewTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function detail($userId) {
        $targetUser = User::where('id', $userId)->first();
        if (!$targetUser) {
            abort(404);
        }
        $user = Auth::user();

        $liveRooms = Room::with(['user'])->where('status', 1)->where('user_id', $userId)->get();
        foreach ($liveRooms as $room) {
            $room->stream_image_path = $room->getStreamImagePath();
        }

        // サポーター
        $payments = Payment::where('payments.created_at', '>=', date('Y-m-01 00:00:00'));
        $payments = $payments
            ->join('messages', function($join) {
                $join->on('payments.message_id', '=', 'messages.id');
            })
            ->join('rooms', function($join) {
                $join->on('messages.room_id', '=', 'rooms.id');
            })
            ->where('rooms.user_id', $userId)
            ->groupBy('payments.user_id', 'rooms.user_id')
            ->orderBy('total_price', 'desc')
            ->select(
                'payments.user_id as payment_user_id',
                DB::raw('SUM(payments.price) as total_price')
            );
        $payments = $payments->get();
        $supporters = [];
        foreach ($payments as $payment) {
            $supporter = User::where('id', $payment->payment_user_id)->first();
            $supporters[] = [
                'id' => $supporter->id,
                'user_name' => $supporter->name,
                'user_image_path' => $supporter->getImagePath(),
                'total_price' => $payment->total_price,
            ];
        }
        $supporters = json_encode($supporters);

        // リスナー
        $listeners = [];
        $userViewTimes = UserViewTime::where('viewed_user_id', $userId)
            ->where('created_at', '>=', date('Y-m-01 00:00:00'))
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

        return view('user.detail', [
            'liveRooms' => $liveRooms,
            'targetUser' => $targetUser,
            'supporters' => $supporters,
            'listeners' => $listeners,
            'user' => $user,
        ]);
    }

    public function getRooms(Request $request) {
        $targetUserId = $request->input('target_user');

        $rooms = Room::where('status', 2)
            ->where('user_id', $targetUserId);

        $rooms = $rooms->with(['user' => function ($q) {
            $q->select('id', 'name');
        }]);

        $rooms = $rooms->orderBy('published_at', 'desc')->paginate(10);
        foreach ($rooms as $room) {
            $room->image_path = $room->getImagePath();
        }
        return $rooms;
    }

    public function getRoomSupporters(Request $request) {
        $roomId = $request->input('room_id');
        $payments = Payment::with(['user', 'message'])
            ->whereHas('message', function($query) use($roomId) {
                $query->where('room_id', $roomId);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        foreach ($payments as $payment) {
            $payment->user->image_path = $payment->user->getImagePath();
        }

        return $payments;
    }

    // for Xcode
    public function registerDeviceToken(Request $request) {
        $user = Auth::user();
        $deviceToken = $request->input('device_token');
        $user->device_token = $deviceToken;
        $user->save();
        return;
    }
}
