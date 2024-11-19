<?php
/*
 * 定期的にシステムがギフトを送る処理
 */

namespace App\Console\Commands;

use App\Events\MessageReceived;
use App\Models\Message;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendGift extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:send-gift';

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
        $rooms = Room::with('game')->where('status', 1)->get();

        foreach ($rooms as $room) {

            // 任天堂のタイトルには適用しない
            if ($room->game) {
                if ($room->game->sales_agency == '任天堂') {
                    continue;
                }
            }

            $now = now();

            $interval = config('services.gift_interval_time');
            $systemUserId = config('services.system_user_id');
            $productId = '1';

            $publishedAt = Carbon::parse($room->published_at);
            $elapsedMinutes = $publishedAt->diffInMinutes($now);

            if ($elapsedMinutes >= $interval && $elapsedMinutes % $interval === 0) {
                $message = new Message();
                $message->user_id = $systemUserId;
                $message->room_id = $room->id;
                $message->content = "ほんのきもち。";
                $message->save();

                $payment = new Payment();
                $payment->is_system = 1;
                $payment->user_id = $systemUserId;
                $payment->message_id = $message->id;
                $payment->product_id = $productId;
                $payment->price = 10;
                $payment->point = 10;
                $payment->save();

                // 加算
                $roomUser = $room->user;
                $roomUser->user_data->point += $payment->point;
                $roomUser->push();

                // イベントを発火
                MessageReceived::dispatch($message);
            }
        }

        return 0;
    }
}
