<?php

namespace App\Console\Commands;

use App\EventRanking;
use App\Room;
use Illuminate\Console\Command;

class UpdateEventRanking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-event-ranking';

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
        if (config('services.event.is_active') == false) {
            return;
        }
        $rooms = Room::where(function($query) {
            $query->where('rooms.status', 1)
                ->orWhere('rooms.status', 2);
            })
            ->where(function($query) {
                $query->where('published_at', '>=', config('services.event.start_date'))
                    ->where('published_at', '<=', config('services.event.end_date'));
            });

        $rooms = $rooms->get();

        // DELETE INSERT
        EventRanking::truncate();
        foreach ($rooms as $room) {
            $eventRaning = EventRanking::create([
                'user_id' => $room->user_id,
                'room_id' => $room->id,
                'room_name' => $room->name,
                'room_image' => $room->image,
                'max_view' => $room->max_view,
            ]);
        }
    }
}
