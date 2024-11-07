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
         * 最大配信時間以上配信したのものはストップさせて、テーブルを更新
         */
        $wowzas = Wowza::where('status', 1)->get();
        if ($wowzas->isEmpty()) {
            return;
        }
        foreach ($wowzas as $wowza) {
            if (strtotime($wowza->started_at) <= strtotime('-' . config('services.max_stream_time') . ' hour')) {
                $this->info(
                    date('Y-m-d H:i:s') . ' [command:update-wowza-status] 配信時間が上限を超えました'
                    . ' wowza_id:' . $wowza->id
                    . ' started_at:' . strtotime($wowza->started_at)
                    . ' strtotime:' . strtotime('-' . config('services.max_stream_time') . ' hour')
                );
                $rooms = Room::where('wowza_id', $wowza->id)->where('status', 1)->get();
                foreach ($rooms as $room) {
                    $room->finish();
                    $room->push();
                }
            }
        }
    }
}
