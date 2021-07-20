@extends('layouts.app')
@section('content')
    <div class="user-detail @if (request()->header('User-Agent') == 'webview') webview @endif">
        <div class="main-content">
            <div class="user-info">
                @if ($targetUser->user_data->rank == 2)
                    <div class="user-rank rank2"><span>推し</span></div>
                @elseif ($targetUser->user_data->rank == 5)
                    <div class="user-rank rank5"><span>公認配信者</span></div>
                @endif
                <div class="user-profile" style="background-image:url({{ $targetUser->getImagePath() }})"></div>
                <div class="user-about">
                    <div class="user-name">{{ $targetUser->name }}</div>
                </div>
            </div>
            <user-component
                :is-web-view="{{ (request()->header('User-Agent') == 'webview') ? "true" : "false" }}"
                :live-rooms="{{ ($liveRooms) ? $liveRooms->toJson() : "{}" }}"
                :target-user="{{ $targetUser->toJson() }}"
                :supporters="{{ $supporters }}"
                :user="{{ ($user) ? $user->toJson() : "{}" }}"
            >
            </user-component>
        </div>
    </div>
@endsection


