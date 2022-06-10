<?php
namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Movie;
use App\Models\MovieGood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovieController extends Controller
{
    public function search(Request $request) {
        $gameId = $request->input('game_id');
        if (!$gameId) {
            return redirect('/');
        }

        $game = Game::where('id', $gameId)->first();
        if (!$game) {
            return redirect('/');
        }

        $movies = Movie::where('is_publish', '1')
            ->where('game_id', $gameId)
            ->orderBy('movies.created_at', 'desc')->get();

        return view('movie.search', [
            'game' => $game,
            'movies' => $movies,
        ]);
    }

    public function detail($movieId) {
        $movie = Movie::with('user')->where('id', $movieId)->first();
        if (!$movie) {
            abort(404);
        }
        $user = Auth::user();
        $movie->image_path = $movie->getImagePath();
        $movie->user->image_path = $movie->user->getImagePath();
        // これを呼んでおかないとVue側でリレーションしてくれない
        $movie->user->user_data;
        $movie->game;

        return view('movie.detail', [
            'movie' => $movie,
            'user' => $user,
        ]);
    }

    /*
     * いいねの一覧取得
     */
    public function getMovieGoods($movieId)
    {
        $movieGoods = MovieGood::where('movie_id', $movieId)->get();
        return $movieGoods;
    }

    /*
     * いいね処理
     */
    public function good(Request $request)
    {
        $movieId = $request->input('movie_id');
        $userId = Auth::id();
        $movieGood = MovieGood::create([
            'user_id' => $userId,
            'movie_id' => $movieId,
        ]);
        return;
    }

    /*
     * いいね解除処理
     */
    public function goodCancel(Request $request)
    {
        $movieId = $request->input('movie_id');
        $userId = Auth::id();
        $movieGood = MovieGood::where([
            'user_id' => $userId,
            'movie_id' => $movieId,
        ])->delete();

        return;
    }
}

