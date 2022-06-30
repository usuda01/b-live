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
    <meta property="og:title" content="B-LIVE｜1万円獲得イベント" />
    <meta property="og:description" content="ショート動画を投稿していいね数１位を目指そう！" />
    <meta property="og:url" content="{{ Request::url() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="{{ Request::getSchemeAndHttpHost() }}/images/event5/ogp-event.png" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="1万円獲得イベント｜B-LIVE" />
    <meta property="twitter:image" content="{{ Request::getSchemeAndHttpHost() }}/images/event5/ogp-event.png" />
    <meta name="twitter:description" content="ショート動画を投稿していいね数１位を目指そう！" />
    <link rel="apple-touch-icon" href="/images/apple-touch-icon.png" />
    <link href="{{ mix('css/fonts.css') }}" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}?param=37" rel="stylesheet">
    <link href="{{ mix('css/all.css') }}?param=37" rel="stylesheet">
    <title>B-LIVE｜イベント第５弾！1万円獲得イベント</title>
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WW3H9DP"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <div id="app">
        <div class="event-event5">
            <div class="fv">
                <img src="/images/event5/fv.jpg">
            </div>

            @if (Config::get('services.event5.start_date') > date('Y-m-d H:i:s'))
                <div class="event-end">
                    ※イベント開始までお待ちください
                </div>
            @endif

            @if (Config::get('services.event5.end_date') < date('Y-m-d H:i:s'))
                <div class="event-end">
                    ※このイベントは終了しました
                </div>
            @endif

            @if (Config::get('services.event5.start_date') < date('Y-m-d H:i:s'))
                <div class="ranking">
                    <h2 class="title">現在のランキング</h2>
                    @foreach ($movies as $movie)
                        <div class="movie-box">
                            <div class="game-title">
                                @if ($movie->game)
                                    <a href="/movie/search?game_id={{ $movie->game_id }}">{{ $movie->game->name }}</a>
                                @else
                                @endif
                            </div>
                            <div class="movie-image">
                                <a href="/movie/detail/{{ $movie->id }}" style="background-image: url({{ $movie->getImagePath() }})"></a>
                            </div>
                            <div class="movie-info">
                                <div class="movie-name">
                                    <a href="/movie/detail/{{ $movie->id }}">{{ $movie->name }}</a>
                                </div>
                                <div class="user-info">
                                    <a class="user-profile" href="/user/{{ $movie->user->id }}" style="background-image: url({{ $movie->user->getImagePath() }})"></a>
                                    <a class="user-name" href="/user/{{ $movie->user->id }}">{{ $movie->user->name }}</a>
                                </div>
                                <div class="count">
                                    <span class="number">{{ $movie->movie_goods_count }}</span>
                                    <span class="text">いいね</span>
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
                        <p>動画のいいね数１位の方に</p>
                        <p>amazon ギフト券 1万円分プレゼント</p>
                    </div>
                    <div>
                        <p>▼イベント期間</p>
                        <p>2022/6/27(月) 0時 ～ 2022/7/7(木) 23:59:59</p>
                    </div>
                    <div>
                        <p>▼参加方法</p>
                        <p>① <a class="twitter" href="https://twitter.com/BLIVE77191685/status/1540265976859480064" target="_blank">この投稿</a>をリツイート＆フォロー</p>
                        <p>② B-LIVEにて、ショート動画を投稿</p>
                    </div>
                    <div>▼対象コンテンツ<br>ゲームのプレイ動画</div>
                </div>
            </div>

            <div class="event-howto">
                <h2 class="title">動画投稿の方法</h2>
                <div class="notice">※PCからのみ投稿可能です。<br>スマホからは投稿できません。</div>
                <div class="detail">
                    <ul>
                        <li class="midashi">① B-LIVEに登録</li>
                        <li class="child margin">SNSアカウントでログイン出来ます</li>
                        <li class="midashi margin">② ログイン後、右上のアイコンから"配信管理"を選択</li>
                        <li class="midashi margin">③ 左メニューの"動画投稿"を選択</li>
                        <li class="midashi">④ 投稿可能な動画について</li>
                        <li class="child">・mp4形式の動画</li>
                        <li class="child">・30秒以内、50Mバイト以内</li>
                        <li class="child">・10本まで投稿可能です</li>
                    </ul>
                </div>
            </div>

            <div class="event-caution">
                <h2 class="title">注意事項</h2>
                <ul class="note">
                    <li>※イベント期間外に投稿した動画はランキングの対象外となります。</li>
                    <li>※対象コンテンツはゲーム動画のみとなります。</li>
                    <li>※サーバーの負荷などにより途中でサイトが落ちたり、アップロードが出来ない状態になってしまった場合、イベントを終了とし、その時点でのランキングで集計します。</li>
                    <li>※イベント終了後、 B-LIVEの公式Twitter（<a class="twitter" href="https://twitter.com/BLIVE77191685">@BLIVE77191685</a>）からDMを送ります。連絡をとれるようにしておいて下さい。</li>
                    <li>※商品の発送は国内のみとなります。</li>
                    <li>※複数アカウントによる作為的ないいね等、不正行為とみられた場合、イベントを辞退していただきます。</li>
                    <li>※常識的に不適切と思われる動画と判断した場合、イベントを辞退していただきます。</li>
                    <li>※管理人（<a href="/user/2">うっすー</a>）も参加し、ランキングの対象とさせていただきます。</li>
                </ul>
            </div>

            <div class="to-top"><a class="" href="/">B-LIVEトップへ</a></div>
        </div>
    </div>
</body>
</html>
