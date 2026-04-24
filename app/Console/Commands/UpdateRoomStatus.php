<?php
/*
 * 配信サーバー (SRS / Wowza) にstreamが送信されていない場合は
 * 配信を終了する
 *
 * SRS_API_URL が設定されていれば SRS モード、未設定なら Wowza モード。
 * Wowza 分岐は移行完了後に削除する。
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
        if ($rooms->isEmpty()) {
            return;
        }

        if (config('services.wowza.api_url')) {
            $this->handleSrs($rooms);
        } else {
            $this->handleWowza($rooms);
        }
    }

    private function handleSrs($rooms)
    {
        // SRS API を 1 回だけ叩いて全ストリーム一覧を取得
        try {
            $response = Http::timeout(10)
                ->get(config('services.wowza.api_url').'/streams/');
        } catch (\Throwable $e) {
            $this->info(date('Y-m-d H:i:s').' [command:update-room-status] API error: '.$e->getMessage());
            return;
        }

        if (!$response->successful()) {
            $this->info(date('Y-m-d H:i:s').' [command:update-room-status] '.$response->body());
            return;
        }

        $activeStreams = collect($response->json('streams', []))
            ->pluck('name')
            ->all();

        foreach ($rooms as $room) {
            if (!in_array($room->wowza->stream_key, $activeStreams, true)) {
                $room->finish();
                $room->push();
            }
        }
    }

    private function handleWowza($rooms)
    {
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
                $body = json_decode($response->body());
                if ($body->bytesIn === 0) {
                    $room->finish();
                    $room->push();
                }
            } else {
                $this->info(date('Y-m-d H:i:s').' [command:update-room-status] '.$response->body());
            }
        }
    }
}
