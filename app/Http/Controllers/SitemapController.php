<?php

namespace App\Http\Controllers;

use App\Models\Movie;

class SitemapController extends Controller
{

    /**
     * サイトマップインデックスを表示する。
     *
     * @return \Illuminate\Http\Response
    */
    public function index()
    {
        $pages = [];
        return response()->view('sitemap.index', [
                'pages' => $pages,
            ])
            ->header('Content-Type', 'text/xml');;
    }

    /**
     * movieページのサイトマップを表示する。
     *
     * @return \Illuminate\Http\Response
     */
    public function movie()
    {
        $movies = Movie::where('is_publish', '1')
            ->orderBy('movies.created_at', 'desc')->get();

        $pages = [];
        foreach ($movies as $movie) {
            $pages []= [
                'url' => url("/movie/detail/{$movie->id}"),
                'modifiedAt' => $movie->updated_at->format('Y-m-d'),
            ];
        }
        return response()->view('sitemap.movie', [
                'pages' => $pages,
            ])
            ->header('Content-Type', 'text/xml');
    }

    /**
     * 静的ページのサイトマップを表示する。
     *
     * @return \Illuminate\Http\Response
     */
    public function page()
    {
        return response()->view('sitemap.page')
            ->header('Content-Type', 'text/xml');
    }
}
