@extends('layouts.app')
@section('content')
    <div class="home-index">
        @include('parts.home_menu')
        <div class="main-area">
            <h2 class="main-title">検索結果</h2>
            <div class="room-content">
            @foreach ($rooms as $room)
                <div class="room-box">
                    <div class="room-image"><a href="/room/{{ $room->id }}" v-bind:style="{ backgroundImage: 'url({{ $room->getStreamImagePath() }})' }"></a></div>
                    <div class="room-info">
                        <a class="room-name" href="/room/{{ $room->id }}">{{ $room->name }}</a>
                        <div class="user-name">{{ $room->user->name }}</div>
                    </div>
                </div>
            @endforeach
            </div>
        </div>
    </div>
@endsection


