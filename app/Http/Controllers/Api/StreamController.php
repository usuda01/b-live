<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessSendMailLiveStarted;
use App\Models\Room;
use App\Models\StreamSchedule;
use App\Models\StreamScheduleReminder;
use App\Models\Wowza;
use App\Services\FcmService;
use Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
                'server_url' => Wowza::buildServerUrl(),
                'stream_key' => $streamKey,
                'hls_url' => Wowza::buildHlsUrl($streamKey),
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

    public function state(Request $request): JsonResponse
    {
        $user = $request->user();

        $room = Room::where('user_id', $user->id)
            ->where('status', 1)
            ->first();

        return response()->json([
            'is_live' => $room !== null,
            'room_id' => $room ? $room->id : null,
        ]);
    }

    public function start(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required_without:schedule_id|max:64',
            'description' => 'max:1000',
            'game_id' => 'nullable|integer',
            'schedule_id' => 'nullable|integer',
            'stream_alert' => 'nullable|boolean',
            'image' => 'nullable|string', // Base64エンコードされた画像
        ]);

        // 予定からの配信開始
        $activeSchedule = null;
        if ($request->input('schedule_id')) {
            $activeSchedule = StreamSchedule::where('id', $request->input('schedule_id'))
                ->where('user_id', $user->id)
                ->first();
            if (!$activeSchedule) {
                return response()->json(['message' => '予定が見つかりません'], 404);
            }
            if (!$activeSchedule->isStartable()) {
                return response()->json([
                    'message' => 'この予定は今すぐ開始できません（開始時刻の前後1時間内のみ）',
                ], 422);
            }
        }

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
                'server_url' => Wowza::buildServerUrl(),
                'stream_key' => $streamKey,
                'hls_url' => Wowza::buildHlsUrl($streamKey),
                'started_at' => date('Y-m-d H:i:s'),
                'status' => 1,
            ]
        );

        // Roomを作成（予定起動時は予定の値で上書き）
        $room = new Room();
        $room->user_id = $user->id;
        $room->game_id = $activeSchedule ? $activeSchedule->game_id : $request->input('game_id');
        $room->name = $activeSchedule ? $activeSchedule->title : $request->input('name');
        $room->description = $activeSchedule ? $activeSchedule->description : $request->input('description');
        $room->published_at = date('Y-m-d H:i:s');
        $room->status = 1;
        $room->wowza_id = $wowza->id;

        // サムネイル画像の保存（Base64）
        if ($request->input('image')) {
            $imageData = $request->input('image');
            // data:image/jpeg;base64, のプレフィックスを除去
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
                $extension = $matches[1];
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
            } else {
                $extension = 'jpg';
            }
            $imageData = base64_decode($imageData);
            if ($imageData) {
                $fileName = Str::random(32) . '.' . $extension;
                Storage::disk('public')->put('rooms/' . $fileName, $imageData);
                Helper::resizeImage(storage_path('app/public/rooms/' . $fileName), 1280);
                $room->image = $fileName;
            }
        }

        // 予定起動でサムネ未指定なら予定のサムネをコピー
        if ($activeSchedule && !$room->image && $activeSchedule->thumbnail) {
            $sourcePath = storage_path('app/public/schedules/' . $activeSchedule->thumbnail);
            if (file_exists($sourcePath)) {
                $newName = uniqid() . '_' . $activeSchedule->thumbnail;
                copy($sourcePath, storage_path('app/public/rooms/' . $newName));
                $room->image = $newName;
            }
        }

        $room->save();

        // 予定とRoomを紐付け
        if ($activeSchedule) {
            $activeSchedule->room_id = $room->id;
            $activeSchedule->status = StreamSchedule::STATUS_LIVE;
            $activeSchedule->save();
        }

        // Wowzaのステータスを更新
        $wowza->started_at = date('Y-m-d H:i:s');
        $wowza->status = 1;
        $wowza->save();

        // フォロワーへの通知
        $streamAlert = $request->input('stream_alert', false);
        if ($streamAlert) {
            $this->sendNotifications($room);

            // 予定からの開始時、リマインド登録者にも開始通知（FCM + LINE のみ）
            if ($activeSchedule) {
                $this->sendScheduleStartedNotifications($room, $activeSchedule);
            }
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

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'room_id' => 'required|integer',
            'name' => 'nullable|max:64',
            'image' => 'nullable|string',
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

        if ($request->filled('name')) {
            $room->name = $request->input('name');
        }

        // サムネイル画像の保存（Base64）
        if ($request->input('image')) {
            $imageData = $request->input('image');
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
                $extension = $matches[1];
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
            } else {
                $extension = 'jpg';
            }
            $imageData = base64_decode($imageData);
            if ($imageData) {
                $fileName = Str::random(32) . '.' . $extension;
                Storage::disk('public')->put('rooms/' . $fileName, $imageData);
                Helper::resizeImage(storage_path('app/public/rooms/' . $fileName), 1280);
                $room->image = $fileName;
            }
        }

        $room->save();

        return response()->json([
            'message' => '配信情報を更新しました',
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

        // 紐付いた予定があれば終了状態に
        $linkedSchedule = StreamSchedule::where('room_id', $room->id)
            ->where('status', StreamSchedule::STATUS_LIVE)
            ->first();
        if ($linkedSchedule) {
            $linkedSchedule->status = StreamSchedule::STATUS_FINISHED;
            $linkedSchedule->save();
        }

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
            if ($follower->followerUser->user_data->notice_live_start != 1) {
                continue;
            }

            // Push通知（FCM）
            if ($follower->followerUser->device_token) {
                app(FcmService::class)->send(
                    $follower->followerUser->device_token,
                    "{$room->user->name}さんの配信が始まりました",
                    $room->name,
                    [
                        'type' => 'live_start',
                        'room_id' => $room->id,
                    ]
                );
            }

            // LINE通知
            if ($follower->followerUser->user_data->is_line_connected == 1) {
                $lineMessage = "{$follower->followerUser->name}さん\n"
                    . "【{$room->user->name}】さんの配信が始まりました！\n"
                    . $room->name . "\n"
                    . config('app.url').'/room/'.$room->id;
                Helper::pushLineMessage($follower->followerUser->line_id, $lineMessage);
            }

            // メール通知
            if ($follower->followerUser->email) {
                ProcessSendMailLiveStarted::dispatch($follower, $room);
            }
        }
    }

    private function sendScheduleStartedNotifications(Room $room, StreamSchedule $schedule): void
    {
        $subscribers = StreamScheduleReminder::with('user.user_data')
            ->where('schedule_id', $schedule->id)
            ->get()
            ->pluck('user')
            ->filter();

        foreach ($subscribers as $subscriber) {
            if ($subscriber->id === $room->user_id) {
                continue;
            }

            // FCM Push
            if ($subscriber->device_token) {
                app(FcmService::class)->send(
                    $subscriber->device_token,
                    "予告していた{$room->user->name}さんの配信が始まりました",
                    $room->name,
                    [
                        'type' => 'schedule_started',
                        'schedule_id' => $schedule->id,
                        'room_id' => $room->id,
                    ]
                );
            }

            // LINE
            if ($subscriber->user_data && $subscriber->user_data->is_line_connected == 1 && $subscriber->line_id) {
                $lineMessage = "{$subscriber->name}さん\n"
                    . "リマインドONにしていた【{$room->user->name}】さんの配信が始まりました！\n"
                    . $room->name . "\n"
                    . config('app.url').'/room/'.$room->id;
                Helper::pushLineMessage($subscriber->line_id, $lineMessage);
            }
        }
    }
}
