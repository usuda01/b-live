@extends('layouts.app')
@section('content')
    <div class="setting-stream">
        @include('parts.mypage_menu')
        <div class="main-content">
            <h2>ライブ配信</h2>
            <div class="stream-form">
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
                @if ($liveRooms->count() < Config::get('services.max_liver') || $room->id)
                    <form enctype="multipart/form-data" method="post" accept-charset="utf-8" class="" action="/setting/stream" id="stream-form">
                        @csrf
                        <input type="hidden" name="mode" value="">
                        @if (!$room->id)
                            <input type="hidden" name="wowza_id" value="{{ $wowza->id }}">
                        @endif
                        <input type="hidden" name="room_id" value="{{ $room->id }}">

                        @if ($room->id && $room->status == '1')
                            <button type="button" class="btn-danger" onclick="doSubmit('end');">配信終了</button>
                        @endif

                        <ul>
                            @if (!$room->id)
                                <li>
                                    <label class="label">プレビュー</label>
                                    <video id="main-video" width="320" controls muted autoplay playsinline loop></video>
<!--
                                    <video id="temp-video" width=320 class="video-js vjs-default-skin" controls muted="muted">
                                         <source
                                             src="{{ $wowza->hls_url }}"
                                             type="application/x-mpegURL"
                                         >
                                    </video>
-->
                                </li>
                            @endif
                            @if ($room->id)
                                <li>
                                    <label class="label">配信ページ</label>
                                    <div><a href="{{ url('/room/' . $room->id) }}">{{ url('/room/' . $room->id) }}</a></div>
                                </li>
                            @endif
                            <li>
                                <label class="label">動画サムネイル</label>
                                <div class="icon-wrapper">
                                    <div class="icon" style="background-image:url({{ $room->getImagePath() }})"></div>
                                    <input type="file" name="image" id="file-01" class="file-01">
                                    <label class="file-mask">
                                        <div class="btn">画像変更</div>
                                        <div id="mask-file-01"></div>
                                    </label>
                                </div>
                            </li>
                            <li>
                                <label class="label">動画タイトル</label>
                                <input type="text" name="name" value="{{ old('name', $room->name) }}" placeholder="動画タイトルを入力してください">
                            </li>
                            <li>
                                <label class="label">概要欄（任意）</label>
                                <textarea  name="description" placeholder="動画の概要">{{ old('description', $room->description) }}</textarea>
                            </li>
                            <li style="color:#ff0000;"><label class="label">ビットレート：〜3500 Kbpsまでにご協力ください</label> </li>
                            <li>
                                @if (!$room->id)
                                    <!-- 新規 -->
                                    <label class="label">ストリームサーバー<span class="btn-copy" id="btn-server-url-copy"><img src="/images/btn-copy.png"></span></label>
                                    <input type="text" value="{{ $wowza->server_url }}" readonly id="server-url">
                                @elseif ($room->status == '1')
                                    <!-- 配信中 -->
                                    <label class="label">ストリームサーバー<span class="btn-copy" id="btn-server-url-copy"><img src="/images/btn-copy.png"></span></label>
                                    <input type="text" value="{{ $room->wowza->server_url }}" readonly id="server-url">
                                @endif
                            </li>
                            <li>
                                @if (!$room->id)
                                    <!-- 新規 -->
                                    <label class="label">ストリームキー<span class="btn-copy" id="btn-stream-key-copy"><img src="/images/btn-copy.png"></span></label>
                                    <input type="text" name="stream_key" value="{{ $wowza->stream_key }}" readonly id="stream-key">
                                @elseif ($room->status == '1')
                                    <!-- 配信中 -->
                                    <label class="label">ストリームキー<span class="btn-copy" id="btn-stream-key-copy"><img src="/images/btn-copy.png"></span></label>
                                    <input type="text" name="stream_key" value="{{ $room->wowza->stream_key }}" readonly id="stream-key">
                                @endif
                            </li>
                            @if ($room->id)
                                <li>
                                    @if ($room->status == '1')
                                        <input name="status" type="hidden" value="1">
                                    @else
                                        <label class="label">ステータス</label>
                                        <select name="status" value="{{ old('status', $room->status) }}">
                                            <option value="2" @if (old('status', $room->status) == '2') selected @endif>公開</option>
                                            <option value="3" @if (old('status', $room->status) == '3') selected @endif>非公開</option>
                                        </select>
                                    @endif
                                </li>
                            @endif
                        </ul>
                        @if (!$room->id)
                            <button type="button" class="btn-default" onclick="doSubmit('create');">配信開始</button>
                        @elseif ($room->status == '1')
                            <button type="button" class="btn-default" onclick="doSubmit('edit');">更新</button>
                        @else
                            <button type="button" class="btn-default" onclick="doSubmit('edit');">更新</button>
                        @endif
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script src="{{ mix('js/stream.js') }}"></script>
    <script>

    @if (!$room->id)
        // TODO どこかでエラーメッセージを出さないようにする
/*
        var videoPlayer = {
            checkInterval: 5, // seconds
            readyStateOneDuration: 0,
            readyStateTwoDuration: 0,

            healthCheck: function() {
                var error = this.player.error();
//                console.log(error);
                if (error) {
                    this.play();
                    return;
                }

                var readyState = this.player.readyState();
                console.log(readyState);
                switch(readyState) {
                    case 0:
                        this.play();
                        return;
                    case 1:
                        this.readyStateOneDuration += this.checkInterval;
                        break;
                    case 2:
                        this.readyStateTwoDuration += this.checkInterval;
                        break;
                    default:
                        return;
                }
                console.log(this.readyStateOneDuration);
                console.log(this.readyStateTwoDuration);
                if (this.readyStateOneDuration >= 30
                    || this.readyStateTwoDuration >= 30) {
                    this.play();
                    return;
                }
            },

            play: function() {
                this.readyStateOneDuration = 0;
                this.readyStateTwoDuration = 0;
                try {
                    // console.log('destroying old player');
                    // this.player.dispose();
                    this.player = null;
                } catch (e) {
                }
                this.player = videojs('temp-video');
                this.player.src({
                    src: '{{ $wowza->hls_url }}',
                    type: 'application/x-mpegURL',
                    withCredentials: false
                });
                this.player.on('error', function() {
                    console.log(this.player.error());
                });
                this.player.play();
            }
        };

        videoPlayer.play();
        setInterval(function() {
            videoPlayer.healthCheck();
        }, videoPlayer.checkInterval * 1000);
*/

        var hls;
        const video = document.getElementById('main-video');
        const videoUrl = '{{ $wowza->hls_url }}';
        if (Hls.isSupported()) {
            hls = new Hls();
            hls.loadSource(videoUrl);
            hls.attachMedia(video);

            hls.on(Hls.Events.MANIFEST_PARSED, function() {
                // ここがうまく処理されない
                console.log('play');
                var hlsPlayer = new Hls();
                hlsPlayer.loadSource(videoUrl);
                hlsPlayer.attachMedia(video);
//                video.play();
            });

            hls.on(Hls.Events.ERROR, function(event, data) {
                if (data.fatal) {
                    switch (data.type) {
                        case Hls.ErrorTypes.NETWORK_ERROR:
                            console.log(
                                "fatal network error encountered, try to recover"
                            );
                            //hls.startLoad();
                            setTimeout(function() {
                                hls.loadSource(videoUrl);
                            }, 3000);
                            break;

                        case Hls.ErrorTypes.MEDIA_ERROR:
                            console.log(
                                "fatal media error encountered, try to recover"
                            );
                            hls.recoverMediaError();
                            break;

                        default:
                            console.log("Couldn't Recover");
                            hls.destroy();
                            break;
                    }
                }
            });

            setInterval(function() {
//                console.log('load');
//                console.log(hls.url);
                //hls.startLoad()
            }, 3000);

        } else if (video.canPlayType("application/vnd.apple.mpegurl")) {
            video.src = videoUrl;
            video.addEventListener("canplay", () => {
              video.play();
            });
        }

    @endif

    function doSubmit(mode) {
        $('input[name="mode"]').val(mode);
        $('#stream-form').submit();
    }

</script>
@endpush
