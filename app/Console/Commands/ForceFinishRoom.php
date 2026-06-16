<?php
/*
 * 運営が不適切と判断した配信を強制終了するコマンド
 *
 * php artisan room:force-finish {roomId} --message="文面"
 *
 * チャットに運営メッセージを流したうえで配信を終了する。
 * 視聴者側はポーリングで status=2 を検知して終了画面に切り替わる。
 */

namespace App\Console\Commands;

use App\Events\MessageReceived;
use App\Models\Message;
use App\Models\Room;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ForceFinishRoom extends Command
{
    protected $signature = 'room:force-finish
        {roomId : 強制終了する配信のRoom ID}
        {--message= : チャットに流す文面}';

    protected $description = '運営が指定した配信を強制終了する';

    private const DEFAULT_MESSAGE = 'この配信は不適切と判断されたため終了しました';

    public function handle()
    {
        $roomId = (int) $this->argument('roomId');
        $content = $this->option('message') ?: self::DEFAULT_MESSAGE;

        if (mb_strlen($content) > 128) {
            $this->error('文面は128文字以内にしてください');
            return 1;
        }

        $room = Room::find($roomId);

        if (!$room) {
            $this->error("Room ID {$roomId} が見つかりません");
            return 1;
        }

        if ((int) $room->status !== 1) {
            $this->warn("Room ID {$roomId} は配信中ではありません。status={$room->status}");
            return 0;
        }

        $this->postMessage($room, $content);

        $room->finish();
        $room->push();

        Log::info('[room:force-finish] 配信を強制終了しました', [
            'room_id' => $room->id,
            'user_id' => $room->user_id,
            'message' => $content,
        ]);

        $this->info("Room ID {$roomId} を強制終了しました");
        $this->info("文面: {$content}");
        return 0;
    }

    /*
     * 運営アカウントとしてチャットにメッセージを流す
     * status を終了にする前に流し、ライブ中の画面に表示させる
     */
    private function postMessage(Room $room, string $content)
    {
        $message = new Message();
        $message->user_id = config('services.admin_user_id');
        $message->room_id = $room->id;
        $message->content = $content;
        $message->save();

        MessageReceived::dispatch($message);
    }
}
