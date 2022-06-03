@extends('layouts.app')
@section('content')
    <div class="setting-profile">
        @include('parts.account_menu')
        <div class="main-content">
            <h2>アカウント</h2>
            <div class="profile-form">
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
                <form enctype="multipart/form-data" method="post" accept-charset="utf-8" class="" action="/setting/profile">
                    @csrf
                    <ul>
                        <li>
                            <label class="label">プロフィールアイコン</label>
                            <div class="icon-wrapper">
                                <div class="icon" style="background-image:url({{ $user->getImagePath() }})"></div>
                                <input type="file" name="image" id="file-01" class="file-01">
                                <label class="file-mask">
                                    <div class="btn">画像変更</div>
                                    <div id="mask-file-01"></div>
                                </label>
                            </div>
                        </li>
                        <li>
                            <label class="label">表示名</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="ニックネーム">
                        </li>
<!--
                        <li>
                            <label class="label">紹介動画（任意）</label>
                        </li>
-->
                        <li>
                            <label class="label">Twitter URL（任意）</label>
                            <input type="text" name="twitter_url" value="{{ old('twitter_url', $user->twitter_url) }}" placeholder="https://twitter.com/BLIVE77191685">
                        </li>
                        <li>
                            <label class="label">プロフィール（任意）</label>
                            <textarea name="profile" placeholder="自己紹介などを記入してください">{{ old('profile', $user->profile) }}</textarea>
                        </li>
                        <li>
                            <label class="label">通知用メールアドレス</label>
                            <input type="text" name="email" value="{{ old('email', $user->email) }}" placeholder="メールアドレス">
                        </li>
                        <li>
                            <label class="label">ランキングに参加する</label>
                            <label><input type="radio" name="join_ranking" value="1" {{ old('join_ranking', $user->user_data->join_ranking) == 1 ? 'checked' : '' }}> 参加する</label>
                            <label><input type="radio" name="join_ranking" value="2" {{ old('join_ranking', $user->user_data->join_ranking) == 2 ? 'checked' : '' }}> 参加しない</label>
                        </li>
                        <li>
                            <label class="label">LINE連携</label>
                            @if ($user->line_id === null)
                                <div class="no-connected-box">
                                    <div class="no-connected">連携されていません</div>
                                </div>
                            @else
                                <div class="connected-box">
                                    <div class="connected">連携済み</div>
                                    <label><input type="checkbox" name="line_disconnect" value="1" {{ (old('line_disconnect') === '1') ? 'checked="checked"' : '' }}><span> 解除する</span></label>
                                </div>
                            @endif
                        </li>
                        @if ($user->line_id !== null)
                            <li>
                                <label class="label">フォローしたユーザーの配信をLINEで通知</label>
                                <label><input type="radio" name="line_notice" value="1" {{ old('line_notice', $user->user_data->line_notice) == 1 ? 'checked' : '' }}> 通知する</label>
                                <label><input type="radio" name="line_notice" value="0" {{ old('line_notice', $user->user_data->line_notice) == 0 ? 'checked' : '' }}> 通知しない</label>
                            </li>
                        @endif
                    </ul>
                    <button type="submit" class="submit">保存</button>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script src="{{ mix('js/profile.js') }}"></script>
@endpush
