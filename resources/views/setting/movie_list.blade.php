@extends('layouts.app')
@section('title', '動画一覧 - ')
@section('content')
    <div class="setting-movie-list">
        @include('parts.mypage_menu')
        <div class="main-content">
            <h2>動画一覧</h2>
            <div class="notice">
                <ul>
                    <li>動画は{{ Config::get('services.max_movie_upload') }}つまでアップロード可能です。</li>
                    <li>動画を削除した場合、紐づくいいね数もリセットされます。</li>
                </ul>
            </div>
            <div class="movie-list-content">
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
                <div class="movie-header">
                    <div class="column">サムネイル</div>
                    <div class="column">ゲームタイトル</div>
                    <div class="column">動画名</div>
                    <div class="column">ステータス</div>
                </div>
                @foreach ($movies as $movie)
                    <div class="movie-row">
                        <div class="image"><a href="/setting/movie/{{ $movie->id }}"><img src="{{ $movie->getImagePath() }}"></a></div>
                        <div class="game-title">{{ ($movie->game) ? $movie->game->name : '' }}</div>
                        <div class="name"><a href="/setting/movie/{{ $movie->id }}">{{ $movie->name }}</a></div>
                        <div class="status">@if ($movie->is_publish == '0') 非公開 @elseif ($movie->is_publish == '1') 公開 @endif</div>
                    </div>
                @endforeach
                <div class="add-movie"><a href="/setting/movie" class="btn-default">動画投稿</a></div>
            </div>
        </div>
    </div>
@endsection

