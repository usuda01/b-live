@extends('layouts.app')
@section('content')
    <div class="movie-search">
        <div class="main-area">
            <h2 class="main-title">{{ $game->name }}</h2>
            <div class="movie-content">
                @foreach ($movies as $movie)
                    <div class="movie-box">
                        <div class="movie-image">
                            <a href="/movie/detail/{{ $movie->id }}" v-bind:style="{ backgroundImage: 'url({{ $movie->getImagePath() }})' }"></a>
                        </div>
                        <div class="movie-info">
                            <a class="movie-name" href="/movie/detail/{{ $movie->id }}">{{ $movie->name }}</a>
                            <div class="user-name"><a href="/user/{{ $movie->user->id }}">{{ $movie->user->name }}</a></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

