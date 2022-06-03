<?php

/*
 * よく配信してくれるユーザー10人をランク2にする
 */

namespace App\Console\Commands;

use App\Models\Room;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateUserRank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-user-rank';

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
        UserData::where('rank', '2')
            ->update(['rank' => '1']);

        $users =  Room::select(
                'rooms.user_id as user_id',
                DB::raw('count(rooms.id) as room_count'),
//                'user_datas.rank as user_rank'
            )
            ->leftJoin('users', 'users.id', '=', 'rooms.user_id')
            ->leftJoin('user_datas', 'users.id', '=', 'user_datas.user_id')
            ->where('published_at', '>=', date('Y-m-d H:i:s', strtotime('-7 day')))
            ->where('user_datas.rank', 1)
            ->where(function($query) {
                $query->orWhere('rooms.status', 1)
                    ->orWhere('rooms.status', 2);
            })
            ->groupBy('rooms.user_id')
            ->orderBy('room_count', 'desc')->limit(10)->get();

        foreach ($users as $user) {
            if ($user->room_count > 1) {
                $user_data = UserData::where('user_id', $user->user_id)->first();
                $user_data->rank = 2;
                $user_data->save();
            }
        }
    }
}
