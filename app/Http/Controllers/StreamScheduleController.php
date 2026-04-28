<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Room;
use App\Models\StreamSchedule;
use App\Models\StreamScheduleReminder;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StreamScheduleController extends Controller
{
    /*
    |---------------------------------------------------------------------------
    | 配信者向け（要ログイン）
    |---------------------------------------------------------------------------
    */

    // 予定一覧
    public function index()
    {
        $user = Auth::user();

        $schedules = StreamSchedule::with('game')
            ->forUser($user->id)
            ->orderBy('scheduled_start_at', 'asc')
            ->get();

        $games = Game::orderBy('name')->get();

        return view('setting.schedule_list', [
            'schedules' => $schedules,
            'games' => $games,
            'maxPerUser' => StreamSchedule::MAX_PER_USER,
        ]);
    }

    // 予定 作成・更新・削除・複製
    public function post(Request $request)
    {
        $user = Auth::user();
        $mode = $request->input('mode');
        $scheduleId = $request->input('schedule_id');

        if ($mode === 'delete') {
            return $this->handleDelete($request, $user, $scheduleId);
        }

        if ($mode === 'duplicate') {
            return $this->handleDuplicate($request, $user, $scheduleId);
        }

        return $this->handleSave($request, $user, $mode, $scheduleId);
    }

    private function handleSave(Request $request, $user, $mode, $scheduleId)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:64',
            'description' => 'nullable|string|max:1000',
            'game_id' => 'nullable|integer|exists:games,id',
            'scheduled_start_at' => 'required|date|after:now',
            'scheduled_end_at' => 'nullable|date|after:scheduled_start_at',
            'status' => 'required|integer|in:1,2',
            'thumbnail' => 'nullable|image|max:5120',
        ], [
            'title.required' => '配信タイトルを入力してください',
            'title.max' => '配信タイトルは64文字以内で入力してください',
            'description.max' => '配信概要は1000文字以内で入力してください',
            'game_id.exists' => '選択されたカテゴリが存在しません',
            'scheduled_start_at.required' => '配信開始日時を入力してください',
            'scheduled_start_at.date' => '配信開始日時の形式が正しくありません',
            'scheduled_start_at.after' => '配信開始日時は現在時刻より後を指定してください',
            'scheduled_end_at.date' => '配信終了日時の形式が正しくありません',
            'scheduled_end_at.after' => '配信終了日時は開始日時より後を指定してください',
            'status.required' => '公開設定を選択してください',
            'status.in' => '公開設定の値が不正です',
            'thumbnail.image' => 'サムネイルは画像ファイルを指定してください',
            'thumbnail.max' => 'サムネイルは5MB以下にしてください',
        ]);

        if ($validator->fails()) {
            return redirect('/setting/schedules')
                ->withErrors($validator)
                ->withInput();
        }

        $startAt = new \DateTime($request->input('scheduled_start_at'));
        $maxFutureDate = (new \DateTime())->modify('+' . StreamSchedule::MAX_FUTURE_MONTHS . ' months');
        if ($startAt > $maxFutureDate) {
            return redirect('/setting/schedules')
                ->withErrors(['scheduled_start_at' => StreamSchedule::MAX_FUTURE_MONTHS . 'ヶ月先までの予定のみ登録できます'])
                ->withInput();
        }

        if ($mode === 'create') {
            $upcomingCount = StreamSchedule::forUser($user->id)
                ->whereIn('status', [StreamSchedule::STATUS_PUBLISHED, StreamSchedule::STATUS_PRIVATE])
                ->where('scheduled_start_at', '>', now())
                ->count();
            if ($upcomingCount >= StreamSchedule::MAX_PER_USER) {
                $request->session()->flash('flash_message', '登録できる予定は' . StreamSchedule::MAX_PER_USER . '件までです');
                return redirect('/setting/schedules');
            }

            $schedule = new StreamSchedule();
            $schedule->user_id = $user->id;
        } else {
            $schedule = StreamSchedule::where('id', $scheduleId)
                ->where('user_id', $user->id)
                ->first();
            if (!$schedule) {
                abort(404);
            }
            if (in_array($schedule->status, [StreamSchedule::STATUS_LIVE, StreamSchedule::STATUS_FINISHED], true)) {
                $request->session()->flash('flash_message', '配信中または終了済みの予定は編集できません');
                return redirect('/setting/schedules');
            }
        }

        $previousStartAt = $schedule->scheduled_start_at;

        $schedule->title = $request->input('title');
        $schedule->description = $request->input('description');
        $schedule->game_id = $request->input('game_id') ?: null;
        $schedule->scheduled_start_at = $request->input('scheduled_start_at');
        $schedule->scheduled_end_at = $request->input('scheduled_end_at') ?: null;
        $schedule->status = (int) $request->input('status');

        $thumbnail = $request->file('thumbnail');
        if ($thumbnail && $thumbnail->isValid()) {
            Helper::resizeImage($thumbnail->getPathname(), 1280);
            $filePath = $thumbnail->store('public/schedules');
            $schedule->thumbnail = str_replace('public/schedules/', '', $filePath);
        }

        $schedule->save();

        // 日時変更があった場合、リマインドの notified_at をリセットして再通知の対象にする
        if ($mode === 'edit' && $previousStartAt && $previousStartAt->ne($schedule->scheduled_start_at)) {
            StreamScheduleReminder::where('schedule_id', $schedule->id)
                ->update(['notified_at' => null]);
            // TODO: 日時変更通知をリマインド登録者に送る（ProcessSendScheduleReminderから派生したJobで実装）
        }

        $request->session()->flash('flash_message', $mode === 'create' ? '予定を登録しました' : '予定を更新しました');
        return redirect('/setting/schedules');
    }

    private function handleDelete(Request $request, $user, $scheduleId)
    {
        $schedule = StreamSchedule::where('id', $scheduleId)
            ->where('user_id', $user->id)
            ->first();
        if (!$schedule) {
            abort(404);
        }

        // キャンセル時はリマインド登録者への通知なし（仕様）
        if ($schedule->status === StreamSchedule::STATUS_LIVE) {
            $request->session()->flash('flash_message', '配信中の予定は削除できません');
            return redirect('/setting/schedules');
        }

        $schedule->reminders()->delete();
        $schedule->delete();

        $request->session()->flash('flash_message', '予定を削除しました');
        return redirect('/setting/schedules');
    }

    private function handleDuplicate(Request $request, $user, $scheduleId)
    {
        $original = StreamSchedule::where('id', $scheduleId)
            ->where('user_id', $user->id)
            ->first();
        if (!$original) {
            abort(404);
        }

        $upcomingCount = StreamSchedule::forUser($user->id)
            ->whereIn('status', [StreamSchedule::STATUS_PUBLISHED, StreamSchedule::STATUS_PRIVATE])
            ->where('scheduled_start_at', '>', now())
            ->count();
        if ($upcomingCount >= StreamSchedule::MAX_PER_USER) {
            $request->session()->flash('flash_message', '登録できる予定は' . StreamSchedule::MAX_PER_USER . '件までです');
            return redirect('/setting/schedules');
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

        $request->session()->flash('flash_message', '予定を複製しました（非公開状態）。日時を調整して公開してください');
        return redirect('/setting/schedules');
    }

    /*
    |---------------------------------------------------------------------------
    | 視聴者向け（公開）
    |---------------------------------------------------------------------------
    */

    // /schedule タイムテーブル
    public function timetable(Request $request)
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

        // 週跨ぎ予定の continuation 表示のため、開始日は週開始の1日前まで遡る
        $schedules = StreamSchedule::with(['user', 'game'])
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
        if (Auth::check()) {
            $reminderIds = StreamScheduleReminder::where('user_id', Auth::id())
                ->pluck('schedule_id')
                ->toArray();
        }

        return view('schedule.timetable', [
            'schedules' => $schedules,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'reminderIds' => $reminderIds,
        ]);
    }

    // /schedule/{id} 予定詳細
    public function show($id)
    {
        $schedule = StreamSchedule::with(['user', 'game', 'room'])
            ->where('id', $id)
            ->whereIn('status', [
                StreamSchedule::STATUS_PUBLISHED,
                StreamSchedule::STATUS_LIVE,
                StreamSchedule::STATUS_FINISHED,
            ])
            ->firstOrFail();

        $isReminded = false;
        if (Auth::check()) {
            $isReminded = StreamScheduleReminder::where('schedule_id', $id)
                ->where('user_id', Auth::id())
                ->exists();
        }

        // 配信中検出: 「この予定で配信開始」で活性化された場合のみ LIVE 扱い
        $activeRoom = null;
        if ($schedule->status === StreamSchedule::STATUS_LIVE && $schedule->room_id) {
            $activeRoom = Room::where('id', $schedule->room_id)
                ->where('status', 1)
                ->first();
        }

        return view('schedule.show', [
            'schedule' => $schedule,
            'isReminded' => $isReminded,
            'activeRoom' => $activeRoom,
        ]);
    }

    // リマインドON
    public function reminderStore($id)
    {
        $schedule = StreamSchedule::published()
            ->where('id', $id)
            ->firstOrFail();

        StreamScheduleReminder::firstOrCreate([
            'schedule_id' => $schedule->id,
            'user_id' => Auth::id(),
        ]);

        return response()->json(['ok' => true]);
    }

    // リマインドOFF
    public function reminderDestroy($id)
    {
        StreamScheduleReminder::where('schedule_id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['ok' => true]);
    }
}
