<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessSendMailLiveStarted;
use App\Models\Room;
use App\Models\Wowza;
use Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StreamController extends Controller
{
    public function config(Request $request): JsonResponse
    {
        $user = $request->user();

        $streamKey = Str::random(8);

        $wowza = Wowza::firstOrCreate(
            ['user_id' => $user->id],
            [
                'server_url' => 'rtmps://' . config('services.wowza.ssl_host_name') . '/blive',
                'stream_key' => $streamKey,
                'hls_url' => 'https://' . config('services.wowza.ssl_host_name') . '/blive/ngrp:' . $streamKey . '_all/playlist.m3u8',
                'started_at' => date('Y-m-d H:i:s'),
                'status' => 1,
            ]
        );

        return response()->json([
            'server_url' => $wowza->server_url,
            'stream_key' => $wowza->stream_key,
            'hls_url' => $wowza->hls_url,
        ]);
    }

    public function start(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|max:64',
            'description' => 'max:1000',
            'game_id' => 'nullable|integer',
            'stream_alert' => 'nullable|boolean',
        ]);

        // 既に配信中の場合はそのRoomを返す
        $existingRoom = Room::where('user_id', $user->id)->where('status', 1)->first();
        if ($existingRoom) {
            return response()->json([
                'message' => '既に配信中です',
                'room' => [
                    'id' => $existingRoom->id,
                    'name' => $existingRoom->name,
                    'status' => $existingRoom->status,
                ],
            ]);
        }

        // Wowzaを取得（なければ作成）
        $streamKey = Str::random(8);
        $wowza = Wowza::firstOrCreate(
            ['user_id' => $user->id],
            [
                'server_url' => 'rtmps://' . config('services.wowza.ssl_host_name') . '/blive',
                'stream_key' => $streamKey,
                'hls_url' => 'https://' . config('services.wowza.ssl_host_name') . '/blive/ngrp:' . $streamKey . '_all/playlist.m3u8',
                'started_at' => date('Y-m-d H:i:s'),
                'status' => 1,
            ]
        );

        // Roomを作成
        $room = new Room();
        $room->user_id = $user->id;
        $room->game_id = $request->input('game_id');
        $room->name = $request->input('name');
        $room->description = $request->input('description');
        $room->published_at = date('Y-m-d H:i:s');
        $room->status = 1;
        $room->wowza_id = $wowza->id;
        $room->save();

        // Wowzaのステータスを更新
        $wowza->started_at = date('Y-m-d H:i:s');
        $wowza->status = 1;
        $wowza->save();

        // フォロワーへの通知
        $streamAlert = $request->input('stream_alert', false);
        if ($streamAlert) {
            $this->sendNotifications($room);
        }

        return response()->json([
            'message' => '配信を開始しました',
            'room' => [
                'id' => $room->id,
                'name' => $room->name,
                'status' => $room->status,
            ],
        ]);
    }

    public function end(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'room_id' => 'required|integer',
        ]);

        $room = Room::where('id', $request->input('room_id'))
            ->where('user_id', $user->id)
            ->where('status', 1)
            ->first();

        if (!$room) {
            return response()->json([
                'message' => '配信中のルームが見つかりません',
            ], 404);
        }

        $room->finish();
        $room->push();

        return response()->json([
            'message' => '配信を終了しました',
            'room' => [
                'id' => $room->id,
                'name' => $room->name,
                'status' => $room->status,
            ],
        ]);
    }

    private function sendNotifications(Room $room): void
    {
        foreach ($room->user->followers as $follower) {
            // LINE通知
            if ($follower->followerUser->user_data->is_line_connected == 1) {
                if ($follower->followerUser->user_data->line_notice == 1) {
                    $lineMessage = "{$follower->followerUser->name}さん\n"
                        . "【{$room->user->name}】さんの配信が始まりました！\n"
                        . $room->name . "\n"
                        . config('app.url').'/room/'.$room->id;
                    Helper::pushLineMessage($follower->followerUser->line_id, $lineMessage);
                }
            }

            // メール通知
            if ($follower->followerUser->user_data->is_notice1 == 1) {
                if ($follower->followerUser->email) {
                    ProcessSendMailLiveStarted::dispatch($follower, $room);
                }
            }
        }
    }
}
