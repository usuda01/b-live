@extends('layouts.app')
@section('title', '掲載クラン - ')
@section('content')
    <div class="setting-group">
        @include('parts.account_menu')
        <div class="main-content">
            <h2>掲載クラン</h2>
            <div class="group-form">
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
                <form enctype="multipart/form-data" method="post" accept-charset="utf-8" class="" action="/setting/group" id="group-form">
                    @csrf
                    <input type="hidden" name="mode" value="">

                    @if ($group->id)
                        <input type="hidden" name="group_id" value="{{ $group->id }}">
                    @endif

                    <ul>
                        <li>
                            <label class="label">ゲームタイトル</label>
                            <input type="text" name="game_title" value="{{ old('game_title', $group->game_title) }}" placeholder="荒野行動">
                        </li>
                        <li>
                            <label class="label">クラン名</label>
                            <input type="text" name="name" value="{{ old('name', $group->name) }}" placeholder="クラン名、チーム名">
                        </li>
                        <li>
                            <label class="label">クラン画像</label>
                            <div class="icon-wrapper">
                                <div class="icon" style="background-image:url({{ $group->getImagePath() }})"></div>
                                <input type="file" name="image" id="file-01" class="file-01">
                                <label class="file-mask">
                                    <div class="btn">画像変更</div>
                                    <div id="mask-file-01"></div>
                                </label>
                            </div>
                        </li>
                        <li>
                            <label class="label">クラン人数</label>
                            <input type="text" name="member_number" value="{{ old('member_number', $group->member_number) }}" placeholder="3">
                        </li>
                        <li>
                            <label class="label">クラン紹介文</label>
                            <textarea  name="description" placeholder="クラン紹介や、参加条件などを記載">{{ old('description', $group->description) }}</textarea>
                        </li>
                        <li>
                            <label class="label">掲載</label>
                            <label><input type="radio" name="is_publish" value="0" {{ old('is_publish', $group->is_publish) == 0 ? 'checked' : '' }}> 掲載しない</label>
                            <label><input type="radio" name="is_publish" value="1" {{ old('is_publish', $group->is_publish) == 1 ? 'checked' : '' }}> 掲載する</label>
                        </li>
                    </ul>

                    @if (!$group->id)
                        <button type="button" class="btn-default" onclick="doSubmit('create');">作成</button>
                    @else
                        <button type="button" class="btn-default" onclick="doSubmit('edit');">更新</button>
                    @endif

                    @if ($group->id)
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
        $('input[name="mode"]').val(mode);
        $('#group-form').submit();
    }
</script>
@endpush
