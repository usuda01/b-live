<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
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
