@extends('layouts.app')
@section('content')
    <div class="setting-group-list">
        @include('parts.account_menu')
        <div class="main-content">
            <h2>クラン一覧</h2>
            <div class="group-list-content">
                @if (session('flash_message'))
                    <div class="flash_message">
                        {{ session('flash_message') }}
                    </div>
                @endif
                @if ($errors->any())
                    <ul class="error">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    </ul>
                @endif
                <div class="group-header">
                    <div class="column">クラン名</div>
                    <div class="column">ゲームタイトル</div>
                    <div class="column">クラン人数</div>
                </div>
                @foreach ($groups as $group)
                    <div class="group-row">
                        <div class="name"><a href="/setting/group/{{ $group->id }}">{{ $group->name }}</a></div>
                        <div class="game-title">{{ $group->game_title }}</div>
                        <div class="member-number">{{ $group->member_number }}人</div>
                    </div>
                @endforeach
                <div class="add-group"><a href="/setting/group" class="btn-default">クラン作成</a></div>
            </div>
        </div>
    </div>
@endsection

