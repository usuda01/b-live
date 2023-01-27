<?php
/*
 * Wowza にstreamが送信されていない場合は
 * 配信を終了する
 */

namespace App\Console\Commands;

use App\Models\Room;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateRoomStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-room-status';

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
        $rooms = Room::where('status', 1)->get();
        foreach ($rooms as $room) {
            $url = 'http://'.config('services.wowza.host').':8087/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/blive/instances/_definst_/incomingstreams/'.$room->wowza->stream_key.'/monitoring/current';
            $response = Http::withBasicAuth(config('services.wowza.username'), config('services.wowza.password'))
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'charset' => 'utf-8',
                ])
                ->get($url);

            if ($response->successful()) {
                $response = json_decode($response->body());
                if ($response->bytesIn === 0) {
                    $room->finish();
                    $room->push();
                }
            } else {
                $this->info(date('Y-m-d H:i:s').' [command:update-room-status] '.$response->body());
            }
        }
    }
}
