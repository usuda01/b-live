<?php

namespace App\Console\Commands;

use App\Models\UserViewTime;
use App\Models\UserViewTimeLog;
use Illuminate\Console\Command;

class DeleteUserViewTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:delete-user-view-times';

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
        // データが肥大化してしまうので、1ヶ月前のログは削除する
        // 1ヶ月前の日付を計算
        $oneMonthAgo = date('Y-m-d H:i:s', strtotime('-1 month'));
        UserViewTimeLog::where('created_at', '<', $oneMonthAgo)->delete();
        UserViewTime::where('created_at', '<', $oneMonthAgo)->delete();
        return 0;
    }
}
