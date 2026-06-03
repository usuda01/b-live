@extends('layouts.app')
@section('title', '通知設定 - ')
@section('content')
    <div class="setting-notice">
        @include('parts.account_menu')
        <div class="main-content">
            <h2>通知設定</h2>
            <div class="notice-form">
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
                <form method="post" accept-charset="utf-8" class="" action="/setting/notice">
                    @csrf
                    <ul>
                        <li>
                            <label class="label">フォローユーザーの配信開始</label>
                            @foreach (['notice_live_start_mail' => 'メール', 'notice_live_start_push' => 'プッシュ通知', 'notice_live_start_line' => 'LINE'] as $name => $channelLabel)
                                <div class="channel">
                                    <span class="channel-label">{{ $channelLabel }}</span>
                                    <label><input type="radio" name="{{ $name }}" value="1" {{ old($name, $user->user_data->$name) == 1 ? 'checked' : '' }}> 通知する</label>
                                    <label><input type="radio" name="{{ $name }}" value="0" {{ old($name, $user->user_data->$name) == 0 ? 'checked' : '' }}> 通知しない</label>
                                </div>
                            @endforeach
                        </li>
                        <li>
                            <label class="label">フォローされたとき</label>
                            @foreach (['notice_follow_mail' => 'メール', 'notice_follow_push' => 'プッシュ通知', 'notice_follow_line' => 'LINE'] as $name => $channelLabel)
                                <div class="channel">
                                    <span class="channel-label">{{ $channelLabel }}</span>
                                    <label><input type="radio" name="{{ $name }}" value="1" {{ old($name, $user->user_data->$name) == 1 ? 'checked' : '' }}> 通知する</label>
                                    <label><input type="radio" name="{{ $name }}" value="0" {{ old($name, $user->user_data->$name) == 0 ? 'checked' : '' }}> 通知しない</label>
                                </div>
                            @endforeach
                        </li>
                    <ul>
                    <button type="submit" class="submit">保存</button>
                </form>
            </div>
        </div>
    </div>
@endsection

