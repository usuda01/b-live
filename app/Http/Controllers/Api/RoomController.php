<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function countViews(Request $request): JsonResponse
    {
        $roomId = $request->input('room_id');
        $ipAddress = $request->ip();

        if (!$roomId) {
            return response()->json(['views' => 0]);
        }

        if (!$ipAddress) {
            $views = Log::where('room_id', $roomId)->count();
            return response()->json(['views' => $views]);
        }

        $log = Log::where('room_id', $roomId)
            ->where('ip_address', $ipAddress)
            ->where('created_at', '>', date('Y-m-d H:i:s', strtotime('-30 second')))
            ->first();
        if (!$log) {
            Log::create([
                'room_id' => $roomId,
                'ip_address' => $ipAddress,
            ]);
        }

        Log::where('created_at', '<=', date('Y-m-d H:i:s', strtotime('-60 second')))->delete();

        $views = Log::where('room_id', $roomId)
            ->where('created_at', '>', date('Y-m-d H:i:s', strtotime('-30 second')))
            ->count();

        $room = Room::find($roomId);
        if ($room && $room->status == 1 && $room->max_view < $views) {
            $room->max_view = $views;
            $room->save();
        }

        return response()->json([
            'views' => $views,
            'status' => $room ? (int) $room->status : null,
        ]);
    }


    public function index(Request $request): JsonResponse
    {
        $rooms = Room::with(['user', 'wowza'])
            ->where('status', 1)
            ->orderBy('published_at', 'desc')
            ->get();

        $data = [];
        foreach ($rooms as $room) {
            $data[] = [
                'id' => $room->id,
                'name' => $room->name,
                'description' => $room->description,
                'image_url' => url($room->getStreamImagePath()),
                'published_at' => $room->published_at ? $room->published_at->toIso8601String() : null,
                'max_view' => (int) $room->max_view,
                'hls_url' => $room->wowza ? $room->wowza->hls_url : null,
                'user' => [
                    'id' => $room->user->id,
                    'name' => $room->user->name,
                    'image_url' => url($room->user->getImagePath()),
                ],
            ];
        }

        return response()->json([
            'rooms' => $data,
        ]);
    }

    public function show(Request $request, $roomId): JsonResponse
    {
        $room = Room::with(['user', 'wowza'])->find($roomId);

        if (!$room) {
            return response()->json(['message' => 'ルームが見つかりません'], 404);
        }

        return response()->json([
            'room' => [
                'id' => $room->id,
                'name' => $room->name,
                'description' => $room->description,
                'image_url' => url($room->getImagePath()),
                'published_at' => $room->published_at ? $room->published_at->toIso8601String() : null,
                'status' => (int) $room->status,
                'max_view' => (int) $room->max_view,
                'hls_url' => $room->wowza ? $room->wowza->hls_url : null,
                'user' => [
                    'id' => $room->user->id,
                    'name' => $room->user->name,
                    'image_url' => url($room->user->getImagePath()),
                ],
            ],
        ]);
    }
}
