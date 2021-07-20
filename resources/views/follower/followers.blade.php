@extends('layouts.app')
@section('content')
    <div class="follower-follows">
        @include('parts.home_menu')
        <div class="main-area">
            <h2>フォロワー</h2>
            <follower-component></follower-component>
        </div>
    </div>
@endsection
