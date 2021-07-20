<?php

namespace App\Http\Controllers;

use App\EventRanking;

class EventRankingController extends Controller
{

    public function index() {
        $rooms = EventRanking::join('rooms', 'event_rankings.room_id', '=', 'rooms.id')
            ->orderBy('event_rankings.max_view', 'desc')
            ->orderBy('rooms.finished_at', 'desc')->get();
        return view('event.index', [
            'rooms' => $rooms,
        ]);
    }
}
