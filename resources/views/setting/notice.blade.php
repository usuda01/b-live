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
                            <label><input type="radio" name="notice_live_start" value="1" {{ old('notice_live_start', $user->user_data->notice_live_start) == 1 ? 'checked' : '' }}> 通知する</label>
                            <label><input type="radio" name="notice_live_start" value="0" {{ old('notice_live_start', $user->user_data->notice_live_start) == 0 ? 'checked' : '' }}> 通知しない</label>
                        </li>
                        <li>
                            <label class="label">フォローされたとき</label>
                            <label><input type="radio" name="notice_follow" value="1" {{ old('notice_follow', $user->user_data->notice_follow) == 1 ? 'checked' : '' }}> 通知する</label>
                            <label><input type="radio" name="notice_follow" value="0" {{ old('notice_follow', $user->user_data->notice_follow) == 0 ? 'checked' : '' }}> 通知しない</label>
                        </li>
                    <ul>
                    <button type="submit" class="submit">保存</button>
                </form>
            </div>
        </div>
    </div>
@endsection

