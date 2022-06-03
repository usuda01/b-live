<?php

namespace App\Console\Commands;

use App\Models\Room;
use App\Models\RoomRanking;
use Illuminate\Console\Command;

class UpdateRoomRanking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-room-ranking';

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
        $rooms = Room::where(function($query) {
            $query->where('rooms.status', 1)
                ->orWhere('rooms.status', 2);
            })
            ->where(function($query) {
                $query->where('published_at', '>=', date('Y-m-1 00:00:00', strtotime('-1 months')))
                    ->where('published_at', '<', date('Y-m-1 00:00:00'));
            });

        $rooms = $rooms
            ->leftJoin('users', 'users.id', '=', 'rooms.user_id')
            ->join('user_datas', function($join) {
                $join->on('users.id', '=', 'user_datas.user_id')
                    ->where('user_datas.join_ranking',  1);
            })
            ->select(
                'rooms.id as room_id',
                'rooms.max_view as max_view',
                'rooms.name',
                'rooms.rank',
                'users.id as user_id',
                'users.name as user_name',
                'user_datas.id',
                'user_datas.point',
                'user_datas.join_ranking'
            );

        $rooms = $rooms->get();

        foreach ($rooms as $room) {
            $roomRanings = RoomRanking::create([
                'rank' => $room->rank,
                'user_id' => $room->user_id,
                'room_id' => $room->room_id,
                'max_view' => $room->max_view,
            ]);
        }
    }
}
