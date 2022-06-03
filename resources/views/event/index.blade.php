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
    <meta name="description" content="B-LIVE（ビーライブ）は、ゲームのライブ配信プラットフォームです！お気に入りの配信者を見つけて、一緒に盛り上がろう！">
    <meta property="og:title" content="B-LIVE｜3万円獲得イベント" />
    <meta property="og:description" content="同接数１位を目指そう！" />
    <meta property="og:url" content="{{ Request::url() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="{{ Request::getSchemeAndHttpHost() }}/images/event/ogp-event.png" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="3万円獲得イベント｜B-LIVE" />
    <meta property="twitter:image" content="{{ Request::getSchemeAndHttpHost() }}/images/event/ogp-event.png" />
    <meta name="twitter:description" content="同接数１位を目指そう！" />
    <link rel="apple-touch-icon" href="/images/apple-touch-icon.png" />
    <link href="{{ mix('css/fonts.css') }}" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}?param=37" rel="stylesheet">
    <link href="{{ mix('css/all.css') }}?param=37" rel="stylesheet">
    <title>B-LIVE｜3万円獲得イベント</title>
    {{-- socket io --}}
    <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script>
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WW3H9DP"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <div id="app">
        <div class="event-index">
            <div class="fv">
                <img src="/images/event/fv.png">
            </div>

            @if (Config::get('services.event.end_date') < date('Y-m-d H:i:s'))
                <div class="event-end">
                    ※このイベントは終了しました
                </div>
            @endif

            @if (Config::get('services.event.start_date') < date('Y-m-d H:i:s'))
                <div class="ranking">
                    <h2 class="title">現在のランキング</h2>
                    <div class="ranking-header"><span class="rank">順位</span><span class="views">同接数</span></div>
                    <ul>
                    @foreach ($rooms as $room)
                        <li class="@if ($loop->first) first @endif">
                            <div class="rank-inner">
                                <span class="rank">{{ $loop->index+1 }}</span><span class="views">{{ $room->max_view }}</span><a class="name" href="/room/{{ $room->id}}">{{ $room->room_name }}</a>
                            </div>
                        </li>
                    @endforeach
                    </ul>
                </div>
            @endif

            <div class="event-detail">
                <h2 class="title">イベント詳細</h2>
                <div class="detail">
                    <div>特典：ランキング１位の方に<br>amazon ギフト券 3万円分</div>
                    <div>イベント期間：<br>2021/06/19(土) 0時 ～ 2021/06/21(月) 0時</div>
                    <div>参加方法：配信するだけ</div>
                </div>
            </div>

            <div class="event-caution">
                <h2 class="title">注意事項</h2>
                <ul class="note">
                    <li>※サーバーの負荷などにより途中でサイトが落ちたり、配信が出来ない状態になってしまった場合、イベントを終了とし、その時点でのランキングで集計します。</li>
                    <li>※イベント終了後、 B-LIVEの公式Twitter（<a class="twitter" href="https://twitter.com/BLIVE77191685">@BLIVE77191685</a>）からDMを送ります。連絡をとれるようにしておいて下さい。</li>
                    <li>※商品の発送は国内のみとなります。</li>
                    <li>※常識的に不適切と思われる配信と判断した場合、イベントを辞退していただきます。</li>
                    <li>※ランキングは１分ごとに更新されます。</li>
                    <li>※僕（うっすー）も参加します。</li>
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
