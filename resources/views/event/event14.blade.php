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
    <meta name="description" content="視聴時間上位を目指そう！">
    <meta property="og:title" content="アマギフ獲得イベント｜B-LIVE" />
    <meta property="og:description" content="視聴時間上位を目指そう！" />
    <meta property="og:url" content="{{ Request::url() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="{{ Request::getSchemeAndHttpHost() }}/images/event14/ogp-event.png" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="アマギフ獲得イベント｜B-LIVE" />
    <meta property="twitter:image" content="{{ Request::getSchemeAndHttpHost() }}/images/event14/ogp-event.png" />
    <meta name="twitter:description" content="視聴時間上位を目指そう！" />
    <link rel="apple-touch-icon" href="/images/apple-touch-icon.png" />
    <link href="{{ mix('css/fonts.css') }}" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}?param=62" rel="stylesheet">
    <link href="{{ mix('css/all.css') }}?param=62" rel="stylesheet">
    <title>イベント第１４弾！視聴時間を競おう！｜B-LIVE</title>
    {{-- socket io --}}
    <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script>
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WW3H9DP"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <div id="app">
        <div class="event-event14">
            <div class="fv">
                <img src="/images/event14/fv.jpg">
            </div>

            @if (Config::get('services.event14.start_date') > date('Y-m-d H:i:s'))
                <div class="event-end">
                    ※イベント開始までお待ちください
                </div>
            @endif

            @if (Config::get('services.event14.end_date') < date('Y-m-d H:i:s'))
                <div class="event-end">
                    ※このイベントは終了しました
                </div>
            @endif

            @if (Config::get('services.event14.start_date') < date('Y-m-d H:i:s'))
                <div class="ranking">
                    <h2 class="title">現在のランキング</h2>
                    <div class="ranking-header"><span class="rank">順位</span><span class="times">視聴時間</span></div>
                    <div class="ranking-body">
                        @foreach ($users as $user)
                            <div class="user-box">
                                <div class="rank-inner">
                                    <div class="rank">{{ $loop->index+1 }}</div>
                                    <a class="user-profile" href="/user/{{ $user->id }}" style="background-image:url({{ $user->getImagePath() }})"></a>
                                    <a class="user-name" href="/user/{{ $user->id }}">{{ $user->name }}</a>
                                    <div class="total-time">{{ $user->total_view_time }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="event-detail">
                <h2 class="title">イベント詳細</h2>
                <div class="detail">
                    <div>
                        <p>▼ランキングについて</p>
                        <p>イベント期間中の総視聴時間で計算されます</p>
                    </div>
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
                        <p>① <a class="twitter" href="https://twitter.com/BLIVE77191685/status/1700045162855235666" target="_blank">この投稿</a>をリポスト＆フォロー</p>
                        <p>② B-LIVEにてアカウント作成</p>
                        <p>③ ライブ配信を視聴</p>
                    </div>
                </div>
            </div>

            <div class="event-caution">
                <h2 class="title">注意事項</h2>
                <ul class="note">
                    <li>※サーバーの負荷などにより途中でサイトが落ちたり、配信が出来ない状態になってしまった場合、イベントを終了とし、その時点でのランキングで集計します。</li>
                    <li>※イベント終了後、 B-LIVEの公式X（<a class="twitter" href="https://twitter.com/BLIVE77191685">@BLIVE77191685</a>）からDMを送ります。連絡をとれるようにしておいて下さい。</li>
                    <li>※メールアドレスでのギフトコード発送となります。</li>
                    <li>※配信終了後のページを閲覧しても試聴時間は加算されません。</li>
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
