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
            foreach ($room->messages()->get() as $message) {
                $message->payment()->delete();
            }
            $room->messages()->delete();
            $room->roomRankings()->delete();
            $room->delete();
        }
    }
}
