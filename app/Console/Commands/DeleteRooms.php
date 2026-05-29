<?php

namespace App\Console\Commands;

use App\Models\Room;
use App\Models\RoomRanking;
use Illuminate\Console\Command;

class DeleteRooms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:delete-rooms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $rooms = Room::where('created_at', '<', date('Y-m-d H:i:s', strtotime('-3 month')))->get();
        foreach ($rooms as $room) {
            foreach ($room->logs()->get() as $log) {
                $log->delete();
            }
            // メッセージごとに個別 delete() でフックを発火させる
            // - payments.message_id は RESTRICT FK なので先に payment を消す
            // - $message->delete() で Message::deleting → MessageImage::deleted が連鎖し、
            //   message_images のストレージ実体も削除される
            // - 旧実装の $room->messages()->delete()（mass delete）はフック非発火で
            //   孤児ファイル化するため使わない
            foreach ($room->messages()->get() as $message) {
                $message->payment()->delete();
                $message->delete();
            }
            $room->roomRankings()->delete();
            $room->delete();
        }
    }
}
