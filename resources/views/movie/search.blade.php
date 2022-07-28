@extends('layouts.app')
@if ($game)
    @section('title', $game->name . '｜ショート動画' . ' - ')
@else
    @section('title', 'ショート動画' . ' - ')
@endif
@section('content')
    <div class="movie-search">
        <div class="main-area">
            <div class="breadcrumbs">
                @if ($game)
                    <span><a href="/movie/search"><span>ショート動画</span></a></span>&nbsp;»&nbsp;<span>{{ $game->name }}</span>
                @else
                    <span>ショート動画</span>
                @endif
            </div>
            <movie-search-component
                :game="{{ ($game) ? $game->toJson() : "{}" }}"
            >
            </movie-search-component>
        </div>
    </div>
@endsection

