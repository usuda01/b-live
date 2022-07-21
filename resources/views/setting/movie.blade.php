@extends('layouts.app')
@section('title', '掲載動画 - ')
@section('content')
    <div class="setting-movie">
        @include('parts.mypage_menu')
        <div class="main-content">
            <h2>掲載動画</h2>
            <div class="movie-form">
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
                <form enctype="multipart/form-data" method="post" accept-charset="utf-8" class="" action="/setting/movie" id="movie-form">
                    @csrf
                    <input type="hidden" name="mode" value="">

                    @if ($movie->id)
                        <input type="hidden" name="movie_id" value="{{ $movie->id }}">
                    @endif

                    <ul>
                        <li>
                            <label class="label">ゲームタイトル（任意）</label>
                            <game-select-component
                                :game-id="{{ ($movie->game_id) ? $movie->game_id : 0 }}"
                            ></game-select-component>
                        </li>
                        <li>
                            <label class="label">動画名</label>
                            <input type="text" name="name" value="{{ old('name', $movie->name) }}" placeholder="Apex 好プレイ">
                        </li>
                        <li>
                            <label class="label">動画サムネイル</label>
                            <div class="icon-wrapper">
                                <div class="icon" style="background-image:url({{ $movie->getImagePath() }})"></div>
                                <input type="file" name="image" id="file-01" class="file-01">
                                <label class="file-mask">
                                    <div class="btn">画像変更</div>
                                    <div id="mask-file-01"></div>
                                </label>
                            </div>
                        </li>
                        @if ($movie->path)
                            <li>
                                <label class="label">アップロード動画</label>
                                <video controls playsinline>
                                    <source src="/storage/movies/{{ $movie->path }}"></source>
                                </video>
                            </li>
                        @else
                            <li>
                                <label class="label">動画アップロード</label>
                                <input type="file" name="movie">
                                <div class="notice">動画サイズは{{ Config::get('services.max_movie_upload_size') }}Mまで、再生時間{{ Config::get('services.max_movie_upload_seconds') }}秒まで可能です</div>
                            </li>
                        @endif
                        <li>
                            <label class="label">掲載</label>
                            <label><input type="radio" name="is_publish" value="0" {{ old('is_publish', $movie->is_publish) == 0 ? 'checked' : '' }}> 掲載しない</label>
                            <label><input type="radio" name="is_publish" value="1" {{ old('is_publish', $movie->is_publish) == 1 ? 'checked' : '' }}> 掲載する</label>
                        </li>
                    </ul>

                    @if (!$movie->id)
                        <button type="button" class="btn-default" onclick="doSubmit('create');">保存</button>
                    @else
                        <button type="button" class="btn-default" onclick="doSubmit('edit');">更新</button>
                    @endif

                    @if ($movie->id)
                        <button type="button" class="btn-danger" onclick="doSubmit('delete');">削除</button>
                    @endif

                </form>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
<script src="{{ mix('js/profile.js') }}"></script>
<script>
    function doSubmit(mode) {
        if (mode == 'delete') {
            if (!confirm('動画を削除しますか？')) {
                return false;
            }
        }
        $('input[name="mode"]').val(mode);
        $('#movie-form').submit();
    }
</script>
@endpush
