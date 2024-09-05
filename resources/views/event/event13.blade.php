<html>
<head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-WW3H9DP');</script>
    <!-- End Google Tag Manager -->
    <meta name="google-site-verification" content="P5MZYRt5GLFguKhLczvQkpUCRLTIkm1LyOF93FHd4bM" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="keywords" content="B-LIVE,ライブ配信,配信,ゲーム,ゲーム実況,ゲーム配信">
    <meta name="description" content="配信して同説数上位を目指そう！">
    <meta property="og:title" content="アマギフ獲得イベント｜B-LIVE" />
    <meta property="og:description" content="配信して同接数１位を目指そう！" />
    <meta property="og:url" content="{{ Request::url() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="{{ Request::getSchemeAndHttpHost() }}/images/event13/ogp-event.png" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="アマギフ獲得イベント｜B-LIVE" />
    <meta property="twitter:image" content="{{ Request::getSchemeAndHttpHost() }}/images/event13/ogp-event.png" />
    <meta name="twitter:description" content="配信して同接数上位を目指そう！" />
    <link rel="apple-touch-icon" href="/images/apple-touch-icon.png" />
    <link href="{{ mix('css/fonts.css') }}" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}?param=62" rel="stylesheet">
    <link href="{{ mix('css/all.css') }}?param=62" rel="stylesheet">
    <title>イベント第１３弾！配信して同接数上位を目指そう！｜B-LIVE</title>
    {{-- socket io --}}
    <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script>
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WW3H9DP"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <div id="app">
        <div class="event-event13">
            <div class="fv">
                <img src="/images/event13/fv.jpg">
            </div>

            @if (Config::get('services.event13.start_date') > date('Y-m-d H:i:s'))
                <div class="event-end">
                    ※イベント開始までお待ちください
                </div>
            @endif

            @if (Config::get('services.event13.end_date') < date('Y-m-d H:i:s'))
                <div class="event-end">
                    ※このイベントは終了しました
                </div>
            @endif

            @if (Config::get('services.event13.start_date') < date('Y-m-d H:i:s'))
                <div class="ranking">
                    <h2 class="title">現在のランキング</h2>
                    @foreach ($rooms as $room)
                        <div class="room-box">
                            <div class="room-rank">{{ $loop->index + 1 }}位</div>
                                @if ($room->game)
                                    <div class="game-title">{{ $room->game->name }}</div>
                                @endif
                                <div class="room-image">
                                <a href="/room/{{ $room->id }}" style="background-image: url({{ $room->getImagePath() }})"></a>
                            </div>
                            <div class="room-info">
                                <div class="room-name">
                                    <a href="/room/{{ $room->id }}">{{ $room->name }}</a>
                                </div>
                                <div class="user-info">
                                    <a class="user-profile" href="/user/{{ $room->user->id }}" style="background-image: url({{ $room->user->getImagePath() }})"></a>
                                    <a class="user-name" href="/user/{{ $room->user->id }}">{{ $room->user->name }}</a>
                                </div>
                                <div class="count">
                                    <span class="text">最大同接数</span>
                                    <span class="number">{{ $room->max_view }}</span>
                                </div>
                            </div>                        
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="event-detail">
                <h2 class="title">イベント詳細</h2>
                <div class="detail">
                    <div>
                        <p>▼特典</p>
                        <p>amazon ギフト券 総額1万円分プレゼント</p>
                        <p>１位：５千円</p>
                        <p>２位：３千円</p>
                        <p>３位：２千円</p>
                    </div>
                    <div>
                        <p>▼イベント期間</p>
                        <p>{{ $displayStartDate }} 0時 ～ {{ $displayEndDate }}</p>
                    </div>
                    <div>
                        <p>▼参加方法</p>
                        <p>① <a class="twitter" href="https://twitter.com/BLIVE77191685/status/1831233327636103266" target="_blank">この投稿</a>をリポスト＆フォロー</p>
                        <p>② B-LIVEにて配信</p>
                    </div>
                </div>
            </div>

            <div class="event-caution">
                <h2 class="title">注意事項</h2>
                <ul class="note">
                    <li>※サーバーの負荷などにより途中でサイトが落ちたり、配信が出来ない状態になってしまった場合、イベントを終了とし、その時点でのランキングで集計します。</li>
                    <li>※イベント終了後、 B-LIVEの公式X（<a class="twitter" href="https://twitter.com/BLIVE77191685">@BLIVE77191685</a>）からDMを送ります。連絡をとれるようにしておいて下さい。</li>
                    <li>※メールアドレスでのギフトコード発送となります。</li>
                    <li>※常識的に不適切と思われる配信と判断した場合、イベントを辞退していただくことがあります。</li>
                    <li>※ランキングは１分ごとに更新されます。</li>
                    <li>※同接数が同じ場合、先に配信された動画が優先されます。</li>
                </ul>
            </div>

            <div class="to-top"><a class="" href="/">B-LIVEトップへ</a></div>
        </div>
    </div>
    <!-- api 認証のための記述 -->
    <script>
        window.Laravel = {!! json_encode([
            'apiToken' => \Auth::user()->api_token ?? null
        ]) !!};
    </script>
    <script src="{{ mix('js/app.js') }}?param=37"></script>
    <script src="{{ mix('js/all.js') }}?param=37"></script>
    <script src="{{ mix('js/common.js') }}?param=37"></script>
</body>
</html>
