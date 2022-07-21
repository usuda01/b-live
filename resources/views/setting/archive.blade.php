@extends('layouts.app')
@section('title', '過去の配信 - ')
@section('content')
    <div class="setting-archive">
        @include('parts.mypage_menu')
        <div class="main-content">
            <h2>過去の配信</h2>
            @if ($streamKey)
<!--
                <div class="archive-download">
                   <a href="http://download.carol-i.com/index.php?file=<?php echo $streamKey; ?>" target="_blank"><img src="/images/btn-download.png"><span>最後に配信した動画をダウンロード</span></a>
                </div>
-->
            @endif
            <div class="archive-content">
                <div class="archive-header">
                    <div class="column">サムネイル</div>
                    <div class="column">タイトル</div>
                    <div class="column">配信日時</div>
                    <div class="column">配信時間</div>
                    <div class="column">ステータス</div>
                </div>
                @foreach ($rooms as $room)
                    <div class="archive-row">
                        <div class="image"><a href="/setting/stream/{{ $room->id }}"><img src="{{ $room->getImagePath() }}"></a></div>
                        <div class="title"><a href="/setting/stream/{{ $room->id }}">{{ $room->name }}</a></div>
                        <div class="date">
                            <div class="published">{{ $room->published_at->format('Y-m-d') }}</div>
                        </div>
                        <div class="time">
                            @if ($room->stream_time)
                                {{ $room->stream_time }}
                            @else
                                計算中
                            @endif
                        </div>
                        <div class="status">@if ($room->status == '1') 公開中 @elseif ($room->status == '2') 公開 @elseif ($room->status == '3') 非公開 @endif</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection


