@extends('layouts.app')
@section('title', $game->name . '｜ショート動画' . ' - ')
@section('content')
    <div class="movie-search">
        <div class="main-area">
            <h2 class="main-title">{{ $game->name }}</h2>
            <movie-search-component
                :game="{{ $game->toJson() }}"
            >
            </movie-search-component>
        </div>
    </div>
@endsection

