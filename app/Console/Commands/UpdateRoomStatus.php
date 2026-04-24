<?php
/*
 * 配信サーバー (SRS) にstreamが送信されていない場合は
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
        if ($rooms->isEmpty()) {
            return;
        }

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
            $alive = in_array($room->wowza->stream_key, $activeStreams, true);
            if (!$alive) {
                $room->finish();
                $room->push();
            }
        }
    }
}
