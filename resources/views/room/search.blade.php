@extends('layouts.app')
@section('content')
    <div class="home-index">
        @include('parts.home_menu')
        <div class="main-area">
            <h2 class="main-title">検索結果</h2>
            <div class="room-content">
                @foreach ($rooms as $room)
                    <div class="room-box">
                        @if ($room->status == '1')
                            <div class="room-image">
                                <a href="/room/{{ $room->id }}" v-bind:style="{ backgroundImage: 'url({{ $room->getStreamImagePath() }})' }"></a>
                                <span class="live">LIVE</span>
                            </div>
                        @elseif ($room->status == '2')
                            <div class="room-image">
                                <a href="/room/{{ $room->id }}" v-bind:style="{ backgroundImage: 'url({{ $room->getImagePath() }})' }"></a>
                            </div>
                        @endif
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


