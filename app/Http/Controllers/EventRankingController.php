<?php

namespace App\Http\Controllers;

use App\Models\EventRanking;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Builder;

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

    public function event2() {

        // いいね数の多い動画
        $movies = Movie::withCount(['movie_goods' => function (Builder $query) {
                $query->where('created_at', '<=', config('services.event2.end_date'));
            }])
            ->where('is_publish', '1')
            ->where('created_at', '<=', config('services.event2.end_date'))
            ->orderBy('movie_goods_count', 'desc')
            ->orderBy('movies.created_at', 'desc')
            ->limit(20)
            ->get();
        return view('event.event2', [
            'movies' => $movies,
        ]);
    }

    public function event3() {

        // いいね数の多い動画
        $movies = Movie::withCount(['movie_goods' => function (Builder $query) {
                $query->where('created_at', '<=', config('services.event3.end_date'));
            }])
            ->where('is_publish', '1')
            ->where('created_at', '>=', config('services.event3.start_date'))
            ->where('created_at', '<=', config('services.event3.end_date'))
            ->orderBy('movie_goods_count', 'desc')
            ->orderBy('movies.created_at', 'desc')
            ->limit(20)
            ->get();
        return view('event.event3', [
            'movies' => $movies,
        ]);
    }
}
