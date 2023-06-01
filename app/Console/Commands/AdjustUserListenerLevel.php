<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AdjustUserListenerLevel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:adjust-user-listener-level';

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
         * 毎月1日に呼ばれる
         * 毎月視聴レベルを10下げる
         */

        $users = User::whereHas('user_data', function ($query) {
            $query->where('listener_level', '>', 10);
        })->get();

        foreach ($users as $user) {
            $user->user_data()->decrement('listener_level', 10);
        }

        return 0;
    }
}
