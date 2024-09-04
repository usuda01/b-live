<?php

namespace App\Http\Controllers;

use App\Models\EventRanking;
use App\Models\Message;
use App\Models\Movie;
use App\Models\Room;
use App\Models\User;
use App\Models\UserViewTimeLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

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

    public function event4() {

        // いいね数の多い動画
        $movies = Movie::withCount(['movie_goods' => function (Builder $query) {
                $query->where('created_at', '<=', config('services.event4.end_date'));
            }])
            ->where('is_publish', '1')
            ->where('created_at', '>=', config('services.event4.start_date'))
            ->where('created_at', '<=', config('services.event4.end_date'))
            ->orderBy('movie_goods_count', 'desc')
            ->orderBy('movies.created_at', 'desc')
            ->limit(20)
            ->get();
        return view('event.event4', [
            'movies' => $movies,
        ]);
    }

    public function event5() {

        // いいね数の多い動画
        $movies = Movie::withCount(['movie_goods' => function (Builder $query) {
                $query->where('created_at', '<=', config('services.event5.end_date'));
            }])
            ->where('is_publish', '1')
            ->where('created_at', '>=', config('services.event5.start_date'))
            ->where('created_at', '<=', config('services.event5.end_date'))
            ->orderBy('movie_goods_count', 'desc')
            ->orderBy('movies.created_at', 'desc')
            ->limit(20)
            ->get();
        return view('event.event5', [
            'movies' => $movies,
        ]);
    }

    public function event6() {

        // いいね数の多い動画
        $movies = Movie::withCount(['movie_goods' => function (Builder $query) {
                $query->where('created_at', '<=', config('services.event6.end_date'));
            }])
            ->where('is_publish', '1')
            ->where('created_at', '>=', config('services.event6.start_date'))
            ->where('created_at', '<=', config('services.event6.end_date'))
            ->orderBy('movie_goods_count', 'desc')
            ->orderBy('movies.created_at', 'desc')
            ->limit(20)
            ->get();
        return view('event.event6', [
            'movies' => $movies,
        ]);
    }

    public function event7() {

        // いいね数の多い動画
        $movies = Movie::withCount(['movie_goods' => function (Builder $query) {
                $query->where('created_at', '<=', config('services.event7.end_date'));
            }])
            ->where('is_publish', '1')
            ->where('created_at', '>=', config('services.event7.start_date'))
            ->where('created_at', '<=', config('services.event7.end_date'))
            ->orderBy('movie_goods_count', 'desc')
            ->orderBy('movies.created_at', 'desc')
            ->limit(20)
            ->get();

        $week = [
            '日', //0
            '月', //1
            '火', //2
            '水', //3
            '木', //4
            '金', //5
            '土', //6
        ];

        $displayStartDate = date('Y/n/j', strtotime(config('services.event7.start_date')));
        $startWeek = $week[date('w', strtotime(config('services.event7.start_date')))];
        $displayStartDate .= "({$startWeek})";

        $displayEndDate = date('Y/n/j', strtotime(config('services.event7.end_date')));
        $endWeek = $week[date('w', strtotime(config('services.event7.end_date')))];
        $displayEndDate .= "({$endWeek})" . " " . date('H:i:s', strtotime(config('services.event7.end_date')));

        return view('event.event7', [
            'movies' => $movies,
            'displayStartDate' => $displayStartDate,
            'displayEndDate' => $displayEndDate,
        ]);
    }

    public function event8() {

        // いいね数の多い動画
        $movies = Movie::withCount(['movie_goods' => function (Builder $query) {
                $query->where('created_at', '<=', config('services.event8.end_date'));
            }])
            ->where('is_publish', '1')
            ->where('created_at', '>=', config('services.event8.start_date'))
            ->where('created_at', '<=', config('services.event8.end_date'))
            ->orderBy('movie_goods_count', 'desc')
            ->orderBy('movies.created_at', 'asc')
            ->limit(20)
            ->get();

        $week = [
            '日', //0
            '月', //1
            '火', //2
            '水', //3
            '木', //4
            '金', //5
            '土', //6
        ];

        $displayStartDate = date('Y/n/j', strtotime(config('services.event8.start_date')));
        $startWeek = $week[date('w', strtotime(config('services.event8.start_date')))];
        $displayStartDate .= "({$startWeek})";

        $displayEndDate = date('Y/n/j', strtotime(config('services.event8.end_date')));
        $endWeek = $week[date('w', strtotime(config('services.event8.end_date')))];
        $displayEndDate .= "({$endWeek})" . " " . date('H:i:s', strtotime(config('services.event8.end_date')));

        return view('event.event8', [
            'movies' => $movies,
            'displayStartDate' => $displayStartDate,
            'displayEndDate' => $displayEndDate,
        ]);
    }

    public function event9() {
        $rooms = EventRanking::join('rooms', 'event_rankings.room_id', '=', 'rooms.id')
            ->orderBy('event_rankings.max_view', 'desc')
            ->orderBy('rooms.created_at', 'asc')->get();

        $week = [
            '日', //0
            '月', //1
            '火', //2
            '水', //3
            '木', //4
            '金', //5
            '土', //6
        ];

        $displayStartDate = date('Y/n/j', strtotime(config('services.event9.start_date')));
        $startWeek = $week[date('w', strtotime(config('services.event9.start_date')))];
        $displayStartDate .= "({$startWeek})";

        $displayEndDate = date('Y/n/j', strtotime(config('services.event9.end_date')));
        $endWeek = $week[date('w', strtotime(config('services.event9.end_date')))];
        $displayEndDate .= "({$endWeek})" . " " . date('H:i:s', strtotime(config('services.event9.end_date')));

        return view('event.event9', [
            'rooms' => $rooms,
            'displayStartDate' => $displayStartDate,
            'displayEndDate' => $displayEndDate,
        ]);
    }

    public function event10() {

        $disableUserIds = [1358];

        $totalTime = Room::select(
                'user_id',
                DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(stream_time))) as total_time'),
                DB::raw('MAX(published_at) AS last_published_at')
            )
            ->where('published_at', '>=', config('services.event10.start_date'))
            ->where('finished_at', '>=', config('services.event10.start_date'))
            ->where('finished_at', '<=', config('services.event10.end_date'))
            ->whereNotIn('user_id', $disableUserIds)
            ->groupBy('user_id')
            ->orderBy('total_time', 'desc')
            ->orderBy('last_published_at', 'desc')
            ->get();

        $users = []; // ランキングに表示するユーザー
        foreach ($totalTime as $row) {
            $user = User::where('id', $row->user_id)->first();
            $user->total_time = $row->total_time;
            $users[] = $user;
        }

        $week = [
            '日', //0
            '月', //1
            '火', //2
            '水', //3
            '木', //4
            '金', //5
            '土', //6
        ];

        $displayStartDate = date('Y/n/j', strtotime(config('services.event10.start_date')));
        $startWeek = $week[date('w', strtotime(config('services.event10.start_date')))];
        $displayStartDate .= "({$startWeek})";

        $displayEndDate = date('Y/n/j', strtotime(config('services.event10.end_date')));
        $endWeek = $week[date('w', strtotime(config('services.event10.end_date')))];
        $displayEndDate .= "({$endWeek})" . " " . date('H:i:s', strtotime(config('services.event10.end_date')));

        return view('event.event10', [
            'displayStartDate' => $displayStartDate,
            'displayEndDate' => $displayEndDate,
            'users' => $users,
        ]);
    }

    /*
     * 自分のコメント、ゲストコメントは対象外とする
     */
    public function event11() {

        $userIds = [];
        $rooms = [];
        $messages = Message::join('rooms', 'messages.room_id', '=', 'rooms.id')
            ->select(
                'messages.room_id AS room_id',
                DB::raw('COUNT(messages.id) AS comment_count'),
            )
            ->where('rooms.published_at', '>=', config('services.event11.start_date'))
            ->where('rooms.finished_at', '<=', config('services.event11.end_date'))
            ->where('messages.created_at', '>=', config('services.event11.start_date'))
            ->where('messages.created_at', '<=', config('services.event11.end_date'))
            ->where('messages.user_id', '<>', config('services.guest_user_id'))
            ->whereRaw('messages.user_id <> rooms.user_id')
            ->groupBy('room_id')
            ->orderBy('comment_count', 'desc')
            ->orderBy('rooms.published_at', 'desc')
            ->get();
        foreach ($messages as $message) {
            $room = $message->room;
            $room->comment_count = $message->comment_count;
            if (in_array($room->user_id, $userIds)) {
                // 何もしない
            } else {
                $userIds[] = $room->user_id;
                $rooms[] = $room;
            }
        }

        $week = [
            '日', //0
            '月', //1
            '火', //2
            '水', //3
            '木', //4
            '金', //5
            '土', //6
        ];

        $displayStartDate = date('Y/n/j', strtotime(config('services.event11.start_date')));
        $startWeek = $week[date('w', strtotime(config('services.event11.start_date')))];
        $displayStartDate .= "({$startWeek})";

        $displayEndDate = date('Y/n/j', strtotime(config('services.event11.end_date')));
        $endWeek = $week[date('w', strtotime(config('services.event11.end_date')))];
        $displayEndDate .= "({$endWeek})" . " " . date('H:i:s', strtotime(config('services.event11.end_date')));

        return view('event.event11', [
            'displayStartDate' => $displayStartDate,
            'displayEndDate' => $displayEndDate,
            'rooms' => $rooms,
        ]);
    }

    public function event12() {

        // イベントに参加しないユーザー
        $disableUserIds = [];

        // リスナー
        $users = []; // ランキングに表示するユーザー
        $userViewTimeLogs = UserViewTimeLog::whereNotIn('viewer_user_id', $disableUserIds)
            ->select(
                'viewer_user_id',
                DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(view_time))) as total_view_time'),
            )
            ->where('created_at', '>=', config('services.event12.start_date'))
            ->where('created_at', '<=', config('services.event12.end_date'))
            ->groupBy('viewer_user_id')
            ->orderBy('total_view_time', 'desc')
            ->get();
        foreach ($userViewTimeLogs as $userViewTimeLog) {
            $user = User::where('id', $userViewTimeLog->viewer_user_id)->first();
            $user->total_view_time = $userViewTimeLog->total_view_time;
            $users[] = $user;
        }

        $week = [
            '日', //0
            '月', //1
            '火', //2
            '水', //3
            '木', //4
            '金', //5
            '土', //6
        ];

        $displayStartDate = date('Y/n/j', strtotime(config('services.event12.start_date')));
        $startWeek = $week[date('w', strtotime(config('services.event12.start_date')))];
        $displayStartDate .= "({$startWeek})";

        $displayEndDate = date('Y/n/j', strtotime(config('services.event12.end_date')));
        $endWeek = $week[date('w', strtotime(config('services.event12.end_date')))];
        $displayEndDate .= "({$endWeek})" . " " . date('H:i:s', strtotime(config('services.event12.end_date')));

        return view('event.event12', [
            'displayStartDate' => $displayStartDate,
            'displayEndDate' => $displayEndDate,
            'users' => $users,
        ]);
    }

    public function event13() {
        $rooms = EventRanking::join('rooms', 'event_rankings.room_id', '=', 'rooms.id')
            ->orderBy('event_rankings.max_view', 'desc')
            ->orderBy('rooms.created_at', 'asc')->get();

        $week = [
            '日', //0
            '月', //1
            '火', //2
            '水', //3
            '木', //4
            '金', //5
            '土', //6
        ];

        $displayStartDate = date('Y/n/j', strtotime(config('services.event13.start_date')));
        $startWeek = $week[date('w', strtotime(config('services.event13.start_date')))];
        $displayStartDate .= "({$startWeek})";

        $displayEndDate = date('Y/n/j', strtotime(config('services.event13.end_date')));
        $endWeek = $week[date('w', strtotime(config('services.event13.end_date')))];
        $displayEndDate .= "({$endWeek})" . " " . date('H:i:s', strtotime(config('services.event13.end_date')));

        return view('event.event13', [
            'rooms' => $rooms,
            'displayStartDate' => $displayStartDate,
            'displayEndDate' => $displayEndDate,
        ]);
    }
}
