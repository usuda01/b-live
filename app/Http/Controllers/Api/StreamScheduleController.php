<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\StreamSchedule;
use App\Models\StreamScheduleReminder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StreamScheduleController extends Controller
{
    /*
    |---------------------------------------------------------------------------
    | 配信者向け（auth:api）
    |---------------------------------------------------------------------------
    */

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $schedules = StreamSchedule::with('game')
            ->withCount('reminders')
            ->forUser($user->id)
            ->orderBy('scheduled_start_at', 'asc')
            ->get();

        return response()->json([
            'schedules' => $schedules->map(function ($s) {
                return $this->ownerScheduleData($s);
            })->values(),
            'max_per_user' => StreamSchedule::MAX_PER_USER,
            'max_future_months' => StreamSchedule::MAX_FUTURE_MONTHS,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'title' => 'required|string|max:64',
            'description' => 'nullable|string|max:1000',
            'game_id' => 'nullable|integer|exists:games,id',
            'scheduled_start_at' => 'required|date|after:now',
            'scheduled_end_at' => 'nullable|date|after:scheduled_start_at',
            'status' => 'required|integer|in:1,2',
            'image' => 'nullable|string',
        ]);

        if ($error = $this->checkFutureLimit($request->input('scheduled_start_at'))) {
            return $error;
        }

        $upcomingCount = StreamSchedule::forUser($user->id)
            ->whereIn('status', [StreamSchedule::STATUS_PUBLISHED, StreamSchedule::STATUS_PRIVATE])
            ->where('scheduled_start_at', '>', now())
            ->count();
        if ($upcomingCount >= StreamSchedule::MAX_PER_USER) {
            return response()->json([
                'message' => '登録できる予定は' . StreamSchedule::MAX_PER_USER . '件までです',
            ], 422);
        }

        $schedule = new StreamSchedule();
        $schedule->user_id = $user->id;
        $this->fillSchedule($schedule, $request);
        $schedule->save();

        $schedule = $schedule->fresh('game');
        $schedule->loadCount('reminders');

        return response()->json([
            'schedule' => $this->ownerScheduleData($schedule),
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $schedule = StreamSchedule::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        if (!$schedule) {
            return response()->json(['message' => '予定が見つかりません'], 404);
        }

        if (in_array($schedule->status, [StreamSchedule::STATUS_LIVE, StreamSchedule::STATUS_FINISHED], true)) {
            return response()->json(['message' => '配信中または終了済みの予定は編集できません'], 422);
        }

        $request->validate([
            'title' => 'required|string|max:64',
            'description' => 'nullable|string|max:1000',
            'game_id' => 'nullable|integer|exists:games,id',
            'scheduled_start_at' => 'required|date|after:now',
            'scheduled_end_at' => 'nullable|date|after:scheduled_start_at',
            'status' => 'required|integer|in:1,2',
            'image' => 'nullable|string',
        ]);

        if ($error = $this->checkFutureLimit($request->input('scheduled_start_at'))) {
            return $error;
        }

        $previousStartAt = $schedule->scheduled_start_at;

        $this->fillSchedule($schedule, $request);
        $schedule->save();

        if ($previousStartAt && $previousStartAt->ne($schedule->scheduled_start_at)) {
            StreamScheduleReminder::where('schedule_id', $schedule->id)
                ->update(['notified_at' => null]);
        }

        $schedule = $schedule->fresh('game');
        $schedule->loadCount('reminders');

        return response()->json([
            'schedule' => $this->ownerScheduleData($schedule),
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $schedule = StreamSchedule::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        if (!$schedule) {
            return response()->json(['message' => '予定が見つかりません'], 404);
        }

        if ($schedule->status === StreamSchedule::STATUS_LIVE) {
            return response()->json(['message' => '配信中の予定は削除できません'], 422);
        }

        $schedule->reminders()->delete();
        $schedule->delete();

        return response()->json(['message' => '予定を削除しました']);
    }

    public function duplicate(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $original = StreamSchedule::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        if (!$original) {
            return response()->json(['message' => '予定が見つかりません'], 404);
        }

        $upcomingCount = StreamSchedule::forUser($user->id)
            ->whereIn('status', [StreamSchedule::STATUS_PUBLISHED, StreamSchedule::STATUS_PRIVATE])
            ->where('scheduled_start_at', '>', now())
            ->count();
        if ($upcomingCount >= StreamSchedule::MAX_PER_USER) {
            return response()->json([
                'message' => '登録できる予定は' . StreamSchedule::MAX_PER_USER . '件までです',
            ], 422);
        }

        $copy = $original->replicate();
        $copy->status = StreamSchedule::STATUS_PRIVATE;
        $copy->room_id = null;
        $copy->scheduled_start_at = now()->addDay()->setTime(20, 0);
        $copy->scheduled_end_at = $original->scheduled_end_at && $original->scheduled_start_at
            ? (clone $copy->scheduled_start_at)->addSeconds(
                $original->scheduled_start_at->diffInSeconds($original->scheduled_end_at)
            )
            : null;
        $copy->save();

        $copy = $copy->fresh('game');
        $copy->loadCount('reminders');

        return response()->json([
            'schedule' => $this->ownerScheduleData($copy),
        ]);
    }

    /*
    |---------------------------------------------------------------------------
    | 視聴者向け（任意認証）
    |---------------------------------------------------------------------------
    */

    public function timetable(Request $request): JsonResponse
    {
        $weekParam = $request->query('week');
        if ($weekParam) {
            try {
                $weekStart = (new \Carbon\Carbon($weekParam))->startOfWeek();
            } catch (\Throwable $e) {
                $weekStart = now()->startOfWeek();
            }
        } else {
            $weekStart = now()->startOfWeek();
        }
        $weekEnd = $weekStart->copy()->addDays(7);

        $schedules = StreamSchedule::with(['user', 'game'])
            ->withCount('reminders')
            ->whereIn('status', [
                StreamSchedule::STATUS_PUBLISHED,
                StreamSchedule::STATUS_LIVE,
                StreamSchedule::STATUS_FINISHED,
            ])
            ->where('scheduled_start_at', '>=', $weekStart->copy()->subDay())
            ->where('scheduled_start_at', '<', $weekEnd)
            ->orderBy('scheduled_start_at')
            ->get();

        $reminderIds = [];
        $authUser = auth('api')->user();
        if ($authUser) {
            $reminderIds = StreamScheduleReminder::where('user_id', $authUser->id)
                ->whereIn('schedule_id', $schedules->pluck('id'))
                ->pluck('schedule_id')
                ->toArray();
        }

        return response()->json([
            'week_start' => $weekStart->toDateString(),
            'week_end' => $weekEnd->toDateString(),
            'schedules' => $schedules->map(function ($s) use ($reminderIds) {
                return $this->publicScheduleData($s, in_array($s->id, $reminderIds, true));
            })->values(),
        ]);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $schedule = StreamSchedule::with(['user', 'game', 'room'])
            ->withCount('reminders')
            ->where('id', $id)
            ->whereIn('status', [
                StreamSchedule::STATUS_PUBLISHED,
                StreamSchedule::STATUS_LIVE,
                StreamSchedule::STATUS_FINISHED,
            ])
            ->first();
        if (!$schedule) {
            return response()->json(['message' => '予定が見つかりません'], 404);
        }

        $isReminded = false;
        $authUser = auth('api')->user();
        if ($authUser) {
            $isReminded = StreamScheduleReminder::where('schedule_id', $id)
                ->where('user_id', $authUser->id)
                ->exists();
        }

        $activeRoomId = null;
        if ($schedule->status === StreamSchedule::STATUS_LIVE && $schedule->room_id) {
            $activeRoom = Room::where('id', $schedule->room_id)
                ->where('status', 1)
                ->first();
            if ($activeRoom) {
                $activeRoomId = $activeRoom->id;
            }
        }

        return response()->json([
            'schedule' => array_merge(
                $this->publicScheduleData($schedule, $isReminded),
                [
                    'description' => $schedule->description,
                    'active_room_id' => $activeRoomId,
                ]
            ),
        ]);
    }

    public function reminderStore(Request $request, $id): JsonResponse
    {
        $schedule = StreamSchedule::published()->where('id', $id)->first();
        if (!$schedule) {
            return response()->json(['message' => '予定が見つかりません'], 404);
        }

        StreamScheduleReminder::firstOrCreate([
            'schedule_id' => $schedule->id,
            'user_id' => $request->user()->id,
        ]);

        return response()->json(['ok' => true]);
    }

    public function reminderDestroy(Request $request, $id): JsonResponse
    {
        StreamScheduleReminder::where('schedule_id', $id)
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json(['ok' => true]);
    }

    /*
    |---------------------------------------------------------------------------
    | ヘルパー
    |---------------------------------------------------------------------------
    */

    private function checkFutureLimit($scheduledStartAt): ?JsonResponse
    {
        $startAt = new \DateTime($scheduledStartAt);
        $maxFutureDate = (new \DateTime())->modify('+' . StreamSchedule::MAX_FUTURE_MONTHS . ' months');
        if ($startAt > $maxFutureDate) {
            return response()->json([
                'message' => StreamSchedule::MAX_FUTURE_MONTHS . 'ヶ月先までの予定のみ登録できます',
            ], 422);
        }
        return null;
    }

    private function fillSchedule(StreamSchedule $schedule, Request $request): void
    {
        $schedule->title = $request->input('title');
        $schedule->description = $request->input('description');
        $schedule->game_id = $request->input('game_id') ?: null;
        $schedule->scheduled_start_at = (new \Carbon\Carbon($request->input('scheduled_start_at')))
            ->setTimezone(config('app.timezone'));
        $schedule->scheduled_end_at = $request->input('scheduled_end_at')
            ? (new \Carbon\Carbon($request->input('scheduled_end_at')))->setTimezone(config('app.timezone'))
            : null;
        $schedule->status = (int) $request->input('status');

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
                Storage::disk('public')->put('schedules/' . $fileName, $imageData);
                $schedule->thumbnail = $fileName;
            }
        }
    }

    private function ownerScheduleData(StreamSchedule $s): array
    {
        return [
            'id' => $s->id,
            'title' => $s->title,
            'description' => $s->description,
            'thumbnail_url' => url($s->getThumbnailPath()),
            'scheduled_start_at' => $s->scheduled_start_at ? $s->scheduled_start_at->toIso8601String() : null,
            'scheduled_end_at' => $s->scheduled_end_at ? $s->scheduled_end_at->toIso8601String() : null,
            'status' => (int) $s->status,
            'room_id' => $s->room_id,
            'game' => $s->game ? ['id' => $s->game->id, 'name' => $s->game->name] : null,
            'reminders_count' => (int) ($s->reminders_count ?? $s->reminders()->count()),
        ];
    }

    private function publicScheduleData(StreamSchedule $s, bool $isReminded): array
    {
        return [
            'id' => $s->id,
            'title' => $s->title,
            'thumbnail_url' => url($s->getThumbnailPath()),
            'scheduled_start_at' => $s->scheduled_start_at ? $s->scheduled_start_at->toIso8601String() : null,
            'scheduled_end_at' => $s->scheduled_end_at ? $s->scheduled_end_at->toIso8601String() : null,
            'status' => (int) $s->status,
            'user' => $s->user ? [
                'id' => $s->user->id,
                'name' => $s->user->name,
                'image_url' => url($s->user->getImagePath()),
            ] : null,
            'game' => $s->game ? ['id' => $s->game->id, 'name' => $s->game->name] : null,
            'is_reminded' => $isReminded,
            'reminders_count' => (int) ($s->reminders_count ?? $s->reminders()->count()),
        ];
    }
}
