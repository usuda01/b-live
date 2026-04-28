<?php

namespace App\Jobs;

use App\Mail\ScheduleReminder;
use App\Models\Notification;
use App\Models\StreamSchedule;
use App\Models\User;
use App\Services\FcmService;
use Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessSendScheduleReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $scheduleId;
    public $userId;

    public function __construct($scheduleId, $userId)
    {
        $this->scheduleId = $scheduleId;
        $this->userId = $userId;
    }

    public function handle()
    {
        $schedule = StreamSchedule::with('user')->find($this->scheduleId);
        $user = User::with('user_data')->find($this->userId);

        if (!$schedule || !$user) {
            return;
        }

        $startAt = $schedule->scheduled_start_at;
        $title = "{$schedule->user->name}さんの配信開始まであと".StreamSchedule::REMIND_MINUTES_BEFORE."分";
        $body = $schedule->title . ' (' . $startAt->format('H:i') . '〜)';
        $url = url('/schedule/' . $schedule->id);

        // DB通知
        try {
            Notification::create([
                'type' => 'user_action',
                'sender_id' => $schedule->user_id,
                'receiver_id' => $user->id,
                'title' => $title,
                'message' => $body,
            ]);
        } catch (\Throwable $e) {
            Log::warning('schedule reminder DB notification failed: ' . $e->getMessage());
        }

        // FCM Push
        if ($user->device_token) {
            try {
                app(FcmService::class)->send(
                    $user->device_token,
                    $title,
                    $body,
                    [
                        'type' => 'schedule_reminder',
                        'schedule_id' => $schedule->id,
                    ]
                );
            } catch (\Throwable $e) {
                Log::warning('schedule reminder FCM failed: ' . $e->getMessage());
            }
        }

        // LINE
        if ($user->user_data && $user->user_data->is_line_connected == 1 && $user->line_id) {
            try {
                $lineMessage = "{$user->name}さん\n{$title}\n{$body}\n{$url}";
                Helper::pushLineMessage($user->line_id, $lineMessage);
            } catch (\Throwable $e) {
                Log::warning('schedule reminder LINE failed: ' . $e->getMessage());
            }
        }

        // メール
        if ($user->email) {
            try {
                Mail::to($user->email)->send(new ScheduleReminder([
                    'recipientName' => $user->name,
                    'streamerName' => $schedule->user->name,
                    'title' => $schedule->title,
                    'scheduledAt' => $startAt->format('Y/n/j H:i'),
                    'imageUrl' => $schedule->getThumbnailPath(),
                    'scheduleId' => $schedule->id,
                ]));
            } catch (\Throwable $e) {
                Log::warning('schedule reminder mail failed: ' . $e->getMessage());
            }
        }
    }
}
