@extends('layouts.app')
@section('title', $group->name . ' - ')
@section('content')
    <div class="group-detail">
        <div class="main-content">
            <div class="group-info">
                <div class="group-profile" style="background-image:url({{ $group->user_image_path }})"></div>
                <div class="group-name">{{ $group->name }}</div>
            </div>
            <div class="game-title">{{ $group->game_title }}</div>
            <div class="user-name">作成者：<a href="/user/{{ $group->user->id }}">{{ $group->user->name }}</a></div>
            <div class="group-member">クラン人数：{{ $group->member_number }}人</div>
            <div class="description">
                {!! nl2br($group->description) !!}
            </div>
        </div>
    </div>
@endsection


