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
    <meta property="og:title" content="B-LIVE｜ライブ配信" />
    <meta property="og:description" content="B-LIVE（ビーライブ）は、ゲームのライブ配信プラットフォームです！お気に入りの配信者を見つけて、一緒に盛り上がろう！" />
    <meta property="og:url" content="{{ Request::url() }}" />
    <meta property="og:type" content="website" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="【ライブ配信サービス】視聴者を増やして楽しく配信しよう！！" />
    <meta name="twitter:description" content="ゲーム配信プラットフォーム、「B-LIVE」！！" />
    @if (Request::is('room/*'))
        @stack('meta')
    @elseif (Request::is('movie/detail/*'))
        @stack('meta')
    @else
        <meta property="og:image" content="{{ Request::getSchemeAndHttpHost() }}/images/ogp.png" />
        <meta property="twitter:image" content="{{ Request::getSchemeAndHttpHost() }}/images/ogp.png" />
    @endif
    <link rel="apple-touch-icon" href="/images/apple-touch-icon.png" />
    <link href="{{ mix('css/fonts.css') }}" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}?param=55" rel="stylesheet">
    <link href="{{ mix('css/all.css') }}?param=55" rel="stylesheet">
    @if (Request::is('/'))
        <title>B-LIVE｜ゲームのライブ配信、ショート動画サイト</title>
    @else
        <title>@yield('title')B-LIVE</title>
    @endif
    @stack('header-script')
    {{-- socket io --}}
    <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script>
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WW3H9DP"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <!-- ドロワーメニュー -->
    <div class="drawer-bg"></div>
    <div class="drawer-menu">
        <div class="drawer-menu_close">
            <a href="#" id="drawer-menu__close-btn"><img src="/images/btn-sidebar-close.png"></a>
        </div>
        <ul class="drawer-menu__list">
            <li><a href="/page/notice"><img src="/images/icon-menu05.png"><span>お知らせ</span></a></li>
            <li><a href="/page/benefits"><img src="/images/icon-menu03.png"><span>配信者特典</span></a></li>
            <li><a href="/page/ranking"><img src="/images/icon-menu07.png"><span>ランキングについて</span></a></li>
            <li><a href="/page/liver"><img src="/images/icon-menu04.png"><span>配信者の方へ</span></a></li>
            <li><a href="/page/howto"><img src="/images/icon-menu02.png"><span>配信方法</span></a></li>
            <li><a href="/page/terms"><img src="/images/icon-menu04.png"><span>利用規約</span></a></li>
            <li><a href="/page/company"><img src="/images/icon-menu08.jpg"><span>運営者</span></a></li>
        </ul>
    </div><!--// .drawer-menu -->

    <!-- お知らせメニュー -->
    <div class="notice-bg"></div>
    <div class="notice-menu">
        <div class="notice-menu_close">
            <a href="#" id="notice-menu__close-btn"><img src="/images/btn-sidebar-close.png"></a>
        </div>
        <ul class="notice-menu__list">
            <li><a href="{{ route('line') }}"><img src="/images/icon-line.png"><span>LINE連携しよう！</span></a></li>
        </ul>
    </div><!--// .notice-menu -->

    <!-- 検索メニュー -->
    <div class="search-bg"></div>
    <div class="search-content">
        <div class="search-content_close">
            <a href="#" id="search-content__close-btn"><img src="/images/btn-search-close.png"></a>
        </div>
        <div class="search-content__box">
            <form method="GET" action="/search">
                <input type="text" name="q" placeholder="タイトル、ユーザー名"><input type="submit" value="">
            </form>
        </div>
    </div><!--// .search-content -->

    @if (Auth::check())
        <div class="account-menu-bg" id="account-menu-bg"></div>
        <div class="account-menu" id="account-menu">
            <div class="user-info">
                <div class="icon" style="background-image:url({{ Auth::user()->getImagePath() }})"></div>
                <div class="name">{{ Auth::user()->name }}</div>
            </div>
            <ul class="menu-list">
                <li class="menu01"><img src="/images/icon-account-menu01.png"><a href="/setting/profile">アカウント</a></li>
                <li class="menu02"><img src="/images/icon-account-menu02.png"><a href="/followers/follows">フォロー</a></li>
                <li class="menu03"><img src="/images/icon-account-menu03.png"><a href="/followers/followers">フォロワー</a></li>
                <li class="menu04"><img src="/images/icon-account-menu04.png"><a href="/setting/archive">配信管理</a></li>
                <li class="menu05"><img src="/images/icon-account-menu05.png"><a href="/auth/logout">ログアウト</a></li>
            </ul>
        </div>
    @endif

    <header id="header" class="site-header {{ class_basename(Route::current()->controller) }}-{{ request()->route()->getActionMethod() }} @if (request()->header('User-Agent') == 'webview') webview @endif">
        <div class="site-header__inner">
            <div id="toggle-slidebar" class="site-header__toggle-sidebar"><a href="#"><img src="/images/btn-sidebar-open.png"></a></div>
            <div class="site-header__logo"><a href="/"><img src="/images/logo.png"></a></div>
            <div class="site-header__search">
                <form method="GET" action="/search">
                    <input type="text" name="q" placeholder="タイトル、ユーザー名"><input type="submit" value="">
                </form>
            </div>
            <div id="toggle-search-slidebar" class="site-header__search-sp">
                <a href="#"><img src="/images/btn-search02.png"></a>
            </div>
            <div id="toggle-notice" class="site-header__notice">
                <a href="#"><i class="fas fa-bell"></i></a>
            </div>
            @if (Auth::check())
                <div class="site-header__account-loggedin">
                    <a href="#" id="site-header__account-btn"><span class="icon" style="background-image:url({{ Auth::user()->getImagePath() }})"></span></a>
                </div>
            @else
                <div class="site-header__account">
                    <a href="/login" class="site-header__login">ログイン</a>
                    <a href="/login" class="site-header__register">登録</a>
                </div>
            @endif
        </div>
    </header>

    @if (!Auth::check() || (Auth::check() && Auth::user()->user_data->is_line_connected == null))
        <div id="line-connect" class="line-connect {{ class_basename(Route::current()->controller) }}-{{ request()->route()->getActionMethod() }}">
            <div class="headline">
                <div class="line-logo"><img src="/images/icon-line.png"></div>
                <div class="line-text">LINE連携で配信通知を受け取ろう！</div>
            </div>
            <a class="btn-connect" href="/setting/line">LINE連携はこちらから</a>
        </div>
    @endif

    <div id="app-install" class="app-install">
        <div class="headline">
            <div class="app-logo"><img src="/images/btn-app.png"></div>
            <!--<div class="app-text">アプリで快適に視聴！</div>-->
            <!--<div class="app-text">アプリなら遅延ほぼ無し！</div>-->
            <div class="app-text">アプリで配信通知を受け取ろう！</div>
        </div>
        <a class="btn-install" href="https://itunes.apple.com/jp/app/b-live-%E3%83%A9%E3%82%A4%E3%83%96%E9%85%8D%E4%BF%A1/id1507393495">今すぐインストール</a>
    </div>

    <div id="app" class="site-content {{ class_basename(Route::current()->controller) }}-{{ request()->route()->getActionMethod() }} @if (request()->header('User-Agent') == 'webview') webview @endif">
        @yield('content')
    </div>

    @if (Request::is('/') || Request::is('room-ranking/*') || Request::is('followers/follows'))
        <footer id="footer" class="footer-menu">
            <ul>
                <li>
                    @if (Request::is('/'))
                        <a href="/" class="active">
                            <img src="/images/btn-footer-menu01_on.png">
                            <span>ホーム</span>
                        </a>
                    @else
                        <a href="/">
                            <img src="/images/btn-footer-menu01.png">
                            <span>ホーム</span>
                        </a>
                    @endif
                </li>
                <li>
                    @if (Request::is('room-ranking/*'))
                        <a href="/room-ranking/0/1" class="active">
                            <img src="/images/btn-footer-menu02_on.png">
                            <span>ランキング</span>
                        </a>
                    @else
                        <a href="/room-ranking/0/1">
                            <img src="/images/btn-footer-menu02.png">
                            <span>ランキング</span>
                        </a>
                    @endif
                </li>
            </ul>
        </footer>
    @endif

    <!-- ログインモーダル -->
    <div id="modal01" class="modal js-modal modal01">
        <div class="modal__bg js-modal-close"></div>
        <div class="modal__content">
            <a class="js-modal-close" href="#"><img src="/images/btn-close02.png"></a>
            <div class="sns-login">
                <h3 class="login-title">SNSでログイン</h3>
                <ul>
                    <li><a href="/auth/line"><img src="/images/btn-line.png"></a></li>
                    <li><a href="/auth/twitter"><img src="/images/btn-twitter.png"></a></li>
                    <li><a href="/auth/facebook"><img src="/images/btn-facebook.png"></a></li>
                    <li><a href="/auth/apple-signin"><img src="/images/btn-apple.png"></a></li>
                </ul>
                <div class="desc">※SNSに勝手にツイートや投稿をすることはありません</div>
                <div class="agree"><a target="_blank" href="/page/terms">利用規約</a>に同意の上、ご利用ください</div>
            </div>
        </div><!--modal__inner-->
    </div><!--modal-->
    <!--// ログインモーダル -->

    <!-- api 認証のための記述 -->
    <script>
        window.Laravel = {!! json_encode([
            'apiToken' => \Auth::user()->api_token ?? null
        ]) !!};
    </script>
    <script src="{{ mix('js/app.js') }}?param=55"></script>
    <script src="{{ mix('js/all.js') }}?param=55"></script>
    <script src="{{ mix('js/common.js') }}?param=55"></script>
    @stack('scripts')
</body>
</html>
