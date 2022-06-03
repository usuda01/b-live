<?php
/*
 * サムネイルが無い動画に画像を設定する
 */

namespace App\Console\Commands;

use App\Models\Room;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class UpdateRoomImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-room-image';

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
        // サムネイル画像が無い場合は設定する
        $rooms = Room::where('status', 1)->whereNull('image')->get();
        foreach ($rooms as $room) {
            $response = Http::withBasicAuth(config('services.wowza.username'), config('services.wowza.password'))
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'charset' => 'utf-8',
                ])
                ->get('http://'.config('services.wowza.host').':8087/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/blive/instances/_definst_/incomingstreams/'.$room->wowza->stream_key.'/monitoring/current');

            if ($response->successful()) {
                $response = json_decode($response->body());
                if ($response->bytesIn > 0) {
                    $path = storage_path('app/public/rooms/').$room->wowza->stream_key.'-'.$room->id.'.png';
                    // -yは上書き
                    $out = shell_exec('ffmpeg -y -i rtmp://'.config('services.wowza.host').':1935/blive/'.$room->wowza->stream_key.' -f image2 -vframes 1 '.$path);
                    if (File::exists($path)) {
                        $room->image = $room->wowza->stream_key.'-'.$room->id.'.png';
                        $room->save();
                    }
                }
            } else {
                $this->info(date('Y-m-d H:i:s').' [command:update-room-image] '.$response->body());
            }
        }
    }
}
