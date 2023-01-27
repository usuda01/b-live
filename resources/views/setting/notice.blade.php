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
                            <label><input type="radio" name="is_notice1" value="1" {{ old('is_notice1', $user->user_data->is_notice1) == 1 ? 'checked' : '' }}> 通知する</label>
                            <label><input type="radio" name="is_notice1" value="0" {{ old('is_notice1', $user->user_data->is_notice1) == 0 ? 'checked' : '' }}> 通知しない</label>
                        </li>
                    <ul>
                    <button type="submit" class="submit">保存</button>
                </form>
            </div>
        </div>
    </div>
@endsection

