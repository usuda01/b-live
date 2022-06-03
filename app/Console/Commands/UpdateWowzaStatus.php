<?php

namespace App\Console\Commands;

use App\Models\Room;
use App\Models\Wowza;
use Illuminate\Console\Command;

class UpdateWowzaStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-wowza-status';

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
        /*
         * 4時間以上配信したのものはストップさせて、テーブルを更新
         */
        $wowzas = Wowza::where('status', 1)->get();
        if ($wowzas->isEmpty()) {
            $this->info(date('Y-m-d H:i:s').' [command:update-wowza-status] 配信者がいません');
            return;
        }
        foreach ($wowzas as $wowza) {
            if (strtotime($wowza->started_at) <= strtotime('-4 hour')) {
                $this->info(date('Y-m-d H:i:s').' [command:update-wowza-status] wowza_id:'.$wowza->id.' started_at:'.strtotime($wowza->started_at).' strtotime:'.strtotime('-4 hour'));
                $wowza->status = 2;
                $rooms = Room::where('wowza_id', $wowza->id)->where('status', 1)->get();
                foreach ($rooms as $room) {
                    $room->status = 2;
                    $room->finished_at = date('Y-m-d H:i:s');
                    $room->save();
                }
                $wowza->finished_at = date('Y-m-d H:i:s');
                $wowza->save();
            }
        }
    }
}
