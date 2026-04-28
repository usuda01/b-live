<?php
/*
 * 配信予定の開始 N 分前 (StreamSchedule::REMIND_MINUTES_BEFORE) のリマインドを
 * リマインド登録者に送信するバッチ。
 *
 * - 毎分実行
 * - すでに通知済み (notified_at IS NOT NULL) は対象外
 * - 公開状態の予定のみ対象
 * - 過ぎた予定 (開始時刻が現在より前) は対象外（送り遅れ防止）
 */

namespace App\Console\Commands;

use App\Jobs\ProcessSendScheduleReminder;
use App\Models\StreamSchedule;
use App\Models\StreamScheduleReminder;
use Illuminate\Console\Command;

class SendScheduleReminders extends Command
{
    protected $signature = 'command:send-schedule-reminders';
    protected $description = '配信予定のリマインド通知をリマインド登録者に送信';

    public function handle()
    {
        $minutesBefore = StreamSchedule::REMIND_MINUTES_BEFORE;
        $now = now();
        // 開始時刻が「現在 + 15分」以内、かつ「現在」以降の予定を対象
        $windowEnd = $now->copy()->addMinutes($minutesBefore);

        $schedules = StreamSchedule::published()
            ->whereBetween('scheduled_start_at', [$now, $windowEnd])
            ->get();

        if ($schedules->isEmpty()) {
            return;
        }

        foreach ($schedules as $schedule) {
            $reminders = StreamScheduleReminder::where('schedule_id', $schedule->id)
                ->whereNull('notified_at')
                ->get();
            foreach ($reminders as $reminder) {
                if ($reminder->user_id === $schedule->user_id) {
                    // 配信者本人にはリマインドしない
                    $reminder->notified_at = $now;
                    $reminder->save();
                    continue;
                }

                ProcessSendScheduleReminder::dispatch($schedule->id, $reminder->user_id);

                $reminder->notified_at = $now;
                $reminder->save();
            }
            $this->info($now->format('Y-m-d H:i:s') . " [send-schedule-reminders] schedule_id={$schedule->id} sent={$reminders->count()}");
        }
    }
}
