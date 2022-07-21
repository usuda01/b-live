@extends('layouts.app')
@section('title', 'フォロー中 - ')
@section('content')
    <div class="follower-follows">
        @include('parts.home_menu')
        <div class="main-area">
            <h2>フォロー中</h2>
            <follow-component></follow-component>
        </div>
    </div>
@endsection
