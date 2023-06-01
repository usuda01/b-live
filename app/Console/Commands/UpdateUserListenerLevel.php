<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserViewTimeLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateUserListenerLevel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-user-listener-level';

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
     * @return int
     */
    public function handle()
    {
        /*
         * dailyで呼ばれる
         * 前日分の試聴時間を計算
         * 1時間視聴で1レベル上げる
         */

        $yesterday = date('Y-m-d 00:00:00', strtotime('-1 day'));
        $userViewTimeLogs = UserViewTimeLog::select(
            'viewer_user_id',
            DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(view_time))) as total_view_time')
        )
            ->where('created_at', '>=', $yesterday)
            ->groupBy('viewer_user_id')
            ->get();
        
        foreach ($userViewTimeLogs as $userViewTimeLog) {
            $totalViewTime = $userViewTimeLog->total_view_time; // 'H:i:s'のstring型

            $pattern = '/^(\d{2}):/';
            preg_match($pattern, $totalViewTime, $matches);
            $hour = intval($matches[1]);

            // レベルの更新
            $user = User::find($userViewTimeLog->viewer_user_id);
            if ($user) {
                $user->user_data()->increment('listener_level', $hour);
            }
        }

        return 0;
    }
}
