<html class="@if (Route::currentRouteName() === 'room_message_viewer') html-room-message-viewer @endif">
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
    <meta property="og:title" content="B-LIVE ライブ配信" />
    <meta property="og:description" content="B-LIVE（ビーライブ）は、ゲームのライブ配信プラットフォームです！お気に入りの配信者を見つけて、一緒に盛り上がろう！" />
    <meta property="og:url" content="{{ Request::url() }}" />
    <meta property="og:image" content="{{ Request::getSchemeAndHttpHost() }}/images/ogp.png" />
    <meta property="og:type" content="website" />
    <link rel="apple-touch-icon" href="/images/apple-touch-icon.png" />
    <link href="{{ mix('css/fonts.css') }}?param=81" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}?param=81" rel="stylesheet">
    <link href="{{ mix('css/all.css') }}?param=81" rel="stylesheet">
    @if (Request::is('/'))
        <title>B-LIVE｜ゲームのライブ配信、ショート動画サイト</title>
    @else
        <title>@yield('title')B-LIVE</title>
    @endif
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
            <li><a href="/"><img src="/images/icon-menu01.png"><span>ホーム</span></a></li>
        </ul>
    </div><!--// .drawer-menu -->

    @if (Auth::check())
        <div class="account-menu-bg" id="account-menu-bg"></div>
        <div class="account-menu" id="account-menu">
            <div class="user-info">
                <div class="icon" style="background-image:url({{ Auth::user()->getImagePath() }})"></div>
                <div class="name">{{ Auth::user()->name }}</div>
            </div>
            <ul class="menu-list">
                <li><img src="/images/icon-account-menu01.png"><a href="/setting/profile">設定</a></li>
                <li><img src="/images/icon-account-menu02.png"><a href="/setting/stream">配信する</a></li>
                <li><img src="/images/icon-account-menu03.png"><a href="/auth/logout">ログアウト</a></li>
            </ul>
        </div>
    @endif

    <div id="app" class="site-content
            {{ class_basename(Route::current()->controller) }}-{{ request()->route()->getActionMethod() }}
            @if (request()->header('User-Agent') == 'webview') webview @endif">
        @yield('content')
    </div>

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
    <script src="{{ mix('js/app.js') }}?param=81"></script>
    <script src="{{ mix('js/all.js') }}?param=81"></script>
    <script src="{{ mix('js/common.js') }}?param=81"></script>
    @stack('scripts')
</body>
</html>
