<?php
namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Room;
use Illuminate\Http\Request;

class SearchController extends Controller
{

    public function index(Request $request, $activeTab) {
        $q = $request->input('q');
        if (!$q) {
            return redirect('/');
        }

        return view('search.index', [
            'activeTab' => $activeTab,
            'q' => $q,
        ]);
    }

    public function searchRooms(Request $request) {
        $q = $request->input('q');

        $rooms = Room::with(['user'])
            ->leftJoin('users as joinUsers', 'joinUsers.id', '=', 'rooms.user_id')
            ->leftJoin('games as joinGames', 'joinGames.id', '=', 'rooms.game_id')
            ->select('rooms.*')
            ->where(function($query) use($q) {
                $query->orWhere('rooms.status', '=', '1')
                    ->orWhere('rooms.status', '=', '2');
                })
            ->where(function($query) use($q) {
                $query->orWhere('rooms.name', 'like', '%' . $q . '%')
                    ->orWhere('joinGames.name', 'like', '%' . $q . '%')
                    ->orWhere('joinUsers.name', 'like', '%' . $q . '%');
            })
            ->orderBy('rooms.published_at', 'desc')->paginate(10);
        // 画像の設定
        foreach ($rooms as $room) {
            $room->stream_image_path = $room->getStreamImagePath();
            $room->image_path = $room->getImagePath();
        }

        return $rooms;
    }

    public function searchMovies(Request $request) {
        $q = $request->input('q');
        $userId = $request->input('user_id');

        $movies = Movie::with(['user'])
            ->leftJoin('users as joinUsers', 'joinUsers.id', '=', 'movies.user_id')
            ->leftJoin('games as joinGames', 'joinGames.id', '=', 'movies.game_id')
            ->select('movies.*')
            ->where('is_publish', '1')
            ->where(function($query) use($q) {
                $query->orWhere('movies.name', 'like', '%' . $q . '%')
                    ->orWhere('joinGames.name', 'like', '%' . $q . '%')
                    ->orWhere('joinUsers.name', 'like', '%' . $q . '%');
            });
        if ($userId) {
            $movies->where('user_id', $userId);
        }
        $movies = $movies->orderBy('movies.created_at', 'desc')->paginate(10);
        // 画像の設定
        foreach ($movies as $movie) {
            $movie->image_path = $movie->getImagePath();
        }

        return $movies;
    }
}
