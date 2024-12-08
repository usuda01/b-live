<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function markAsRead(Request $request)
    {
        $notificationId = $request->input('notification_id');

        if (!$notificationId) {
            return response()->json(['message' => 'Notification ID is required.'], 400);
        }

        $notification = Notification::where('id', $notificationId)
            ->where('receiver_id', Auth::id())
            ->first();

        if (!$notification) {
            return response()->json(['message' => 'Notification not found.'], 404);
        }

        // 既読処理
        $notification->is_read = 1;
        $notification->read_at = now();
        $notification->save();

        return response()->json(['message' => 'Notification marked as read.']);
    }
}
