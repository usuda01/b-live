<?php
namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Group;
use App\Models\Movie;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    // トップ
    public function index(Request $request)
    {
        $rooms = Room::where('status', 1)->orderBy('published_at', 'desc')->get();

        $movies = Movie::where('is_publish', 1)
            ->orderBy('created_at', 'desc')->limit(8)->get();

        // 配信者の多いタイトル
/*
        $mainGameRooms = Room::select(
                'rooms.game_id as game_id',
                DB::raw('count(rooms.game_id) as room_count')
            )
            ->whereNotNull('rooms.game_id')
            ->groupBy('rooms.game_id')
            ->orderBy('room_count', 'desc')->limit(6)->get();
        $mainGames = [];
        foreach ($mainGameRooms as $mainGameRoom) {
            $game = Game::where('id', $mainGameRoom->game_id)->first();
            $mainGames []= $game;
        }
*/
        // 人気のショート動画
        $popularMovies = Movie::where('is_publish', 1)
            ->orderBy('views', 'desc')
            ->orderBy('id', 'desc')
            ->limit(16)->get();

        // 投稿動画の多いタイトル
        $mainGameMovies = Movie::select(
                'movies.game_id as game_id',
                DB::raw('count(movies.game_id) as movie_count')
            )
            ->whereNotNull('movies.game_id')
            ->groupBy('movies.game_id')
            ->orderBy('movie_count', 'desc')->limit(6)->get();
        $mainGames = [];
        foreach ($mainGameMovies as $mainGameMovie) {
            $game = Game::where('id', $mainGameMovie->game_id)->first();
            $mainGames []= $game;
        }

        // 配信頻度高い
        $frequentUsers = Room::select('rooms.user_id as user_id', DB::raw('count(rooms.id) as room_count'))
            ->leftJoin('users', 'users.id', '=', 'rooms.user_id')
            ->where('published_at', '>=', date('Y-m-d H:i:s', strtotime('-7 day')))
            ->where('stream_time', '>=', '00:10:00')
            ->where(function($query) {
                $query->orWhere('rooms.status', 1)
                    ->orWhere('rooms.status', 2);
            })
            ->groupBy('rooms.user_id')
            ->orderBy('room_count', 'desc')
            ->limit(10)->get();

        foreach ($frequentUsers as $frequentUser) {
            $user = User::where('id', $frequentUser->user_id)->first();
            $frequentUser->name = $user->name;
            $frequentUser->user_image_path = $user->getImagePath();
        }

        // 公認配信者
        $officialUsers = User::select('users.id as user_id', 'users.name as name')
            ->leftJoin('user_datas', 'users.id', '=', 'user_datas.user_id')
            ->where(['user_datas.rank' => 5])->get();
        foreach ($officialUsers as $officialUser) {
            $user = User::where('id', $officialUser->user_id)->first();
            $officialUser->user_image_path = $user->getImagePath();
        }

        // 今月の同接数ランキング
        $recentRooms = Room::select(
                'user_id',
                DB::raw('MAX(max_view) as max_view'),
            )
            ->where(function($query) {
            $query->where('rooms.status', 1)
                ->orWhere('rooms.status', 2);
            })
            ->where(function($query) {
                $query->where('published_at', '>=', date('Y-m-01 00:00:00'))
                    ->where('published_at', '<', date('Y-m-01 00:00:00', strtotime('+1 months')));
            })
            ->groupBy('user_id')
            ->orderBy('max_view', 'desc')
//            ->orderBy('status', 'asc')
//            ->orderBy('finished_at', 'desc')
            ->limit(10)
            ->get();
        $connectionUsers = [];
        foreach ($recentRooms as $recentRoom) {
            $user = User::where('id', $recentRoom->user_id)->first();
            $user->max_view = $recentRoom->max_view;
            $connectionUsers[] = $user;
        }

        // 今月のギフト獲得ランキング
        $paidRooms = Room::select('rooms.user_id as user_id', DB::raw('SUM(payments.price) as sum_price'))
            ->leftJoin('messages', 'rooms.id', '=', 'messages.room_id')
            ->leftJoin('payments', 'messages.id', '=', 'payments.message_id')
            ->where('payments.created_at', '>=', date('Y-m-01 00:00:00'))
            ->where('payments.created_at', '<', date('Y-m-01 00:00:00', strtotime('+1 months')))
            ->groupBy('user_id')
            ->orderBy('sum_price', 'desc')
            ->limit(10)->get();
        $paidUsers = [];
        foreach ($paidRooms as $paidRoom) {
            $user = User::where('id', $paidRoom->user_id)->first();
            $user->sum_price = $paidRoom->sum_price;
            $paidUsers[] = $user;
        }

        // 今月のギフトメッセージ投げた人ランキング
        $payments = Payment::select(
            'user_id',
            DB::raw('SUM(price) as sum_price'),
        )
        ->where('created_at', '>=', date('Y-m-01 00:00:00'))
        ->groupBy('user_id')
        ->orderBy('sum_price', 'desc')->limit(10)->get();
        $paymentUsers = [];
        foreach ($payments as $payment) {
            $user = User::where('id', $payment->user_id)->first();
            $user->sum_price = $payment->sum_price;
            $paymentUsers[] = $user;
        }

        // フォロー数の多いユーザー
        $recentRooms = Room::select('rooms.user_id as user_id')
            ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime('-7 day')))
            ->groupBy('user_id')
            ->get();
        $followerUsers = [];
        $sortKey = [];
        foreach ($recentRooms as $recentRoom) {
            $user = $recentRoom->user;
            $followerUsers[] = $user;
            $sortKey[] = $user->followers->count();
        }
        if ($followerUsers) {
            array_multisort($sortKey, SORT_DESC, $followerUsers);
        }

        // 新着ユーザー
        $newUsers = User::orderBy('created_at', 'desc')->limit(10)->get();

        $archiveRooms = Room::where('status', 2)->orderBy('finished_at', 'desc')->limit(4)->get();

        $groups = Group::where('is_publish', '1')->inRandomOrder()->get();
        foreach($groups as $group) {
            $user = User::where('id', $group->user_id)->first();
            $group->user_image_path = $user->getImagePath();
        }

        return view('home', [
            'archiveRooms' => $archiveRooms,
            'connectionUsers' => $connectionUsers,
            'groups' => $groups,
            'mainGames' => $mainGames,
            'movies' => $movies,
            'officialUsers' => $officialUsers,
            'frequentUsers' => $frequentUsers,
            'followerUsers' => $followerUsers,
            'newUsers' => $newUsers,
            'paidUsers' => $paidUsers,
            'paymentUsers' => $paymentUsers,
            'popularMovies' => $popularMovies,
            'rooms' => $rooms,
        ]);
    }

}
