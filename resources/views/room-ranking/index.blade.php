@extends('layouts.app')
@section('title', '同時接続数ランキング' . ' - ')
@section('content')
    <div class="room-ranking-index">
        @include('parts.home_menu')
        <div class="main-area">
            <room-ranking-component
                :target-month="{{ $targetMonth }}"
                :target-rank="{{ $targetRank }}"
            >
            </room-ranking-component>
        </div>
    </div>
@endsection
