<?php
/*
 * 開始時刻が過ぎたまま LIVE にも遷移していない予定を「終了」状態に変える。
 *
 * - 毎時実行
 * - 公開状態のまま開始時刻 + 24時間 を超えた予定を終了扱いに
 * - 配信中(LIVE) の予定は触らない（streamPost('end') で終了させる）
 */

namespace App\Console\Commands;

use App\Models\StreamSchedule;
use Illuminate\Console\Command;

class ClosePastSchedules extends Command
{
    protected $signature = 'command:close-past-schedules';
    protected $description = '時刻過ぎの予定を終了状態に遷移';

    public function handle()
    {
        $threshold = now()->subDay();

        $count = StreamSchedule::where('status', StreamSchedule::STATUS_PUBLISHED)
            ->where('scheduled_start_at', '<', $threshold)
            ->update(['status' => StreamSchedule::STATUS_FINISHED]);

        if ($count > 0) {
            $this->info(now()->format('Y-m-d H:i:s') . " [close-past-schedules] closed {$count} schedules");
        }
    }
}
