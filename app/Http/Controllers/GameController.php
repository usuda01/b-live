<?php

namespace App\Http\Controllers;

use App\Models\Game;

class GameController extends Controller
{
    public function getGames()
    {
        $games = Game::orderBy('name')
            ->get();
        return $games;
    }
}
