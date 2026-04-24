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
        // サムネイル画像が無い配信中 Room を対象
        $rooms = Room::where('status', 1)->whereNull('image')->get();
        if ($rooms->isEmpty()) {
            return;
        }

        // SRS API で配信中ストリーム一覧を取得
        try {
            $response = Http::timeout(10)
                ->get(config('services.wowza.api_url').'/streams/');
        } catch (\Throwable $e) {
            $this->info(date('Y-m-d H:i:s').' [command:update-room-image] API error: '.$e->getMessage());
            return;
        }
        if (!$response->successful()) {
            $this->info(date('Y-m-d H:i:s').' [command:update-room-image] '.$response->body());
            return;
        }
        $activeStreams = collect($response->json('streams', []))->pluck('name')->all();

        $host = config('services.wowza.ssl_host_name');
        $app  = config('services.wowza.app');

        foreach ($rooms as $room) {
            if (!in_array($room->wowza->stream_key, $activeStreams, true)) {
                continue;  // 配信中でないのでスキップ
            }

            $path = storage_path('app/public/rooms/').$room->wowza->stream_key.'-'.$room->id.'.png';

            // Laravel→SRS は UFW で許可されている 1935 平文 RTMP 経由でサムネ切出し
            $cmd = sprintf(
                'ffmpeg -y -i rtmp://%s:1935/%s/%s -f image2 -vframes 1 %s 2>&1',
                escapeshellarg($host),
                escapeshellarg($app),
                escapeshellarg($room->wowza->stream_key),
                escapeshellarg($path)
            );
            shell_exec($cmd);

            if (File::exists($path)) {
                $room->image = $room->wowza->stream_key.'-'.$room->id.'.png';
                $room->save();
            }
        }
    }
}
