$(function() {
    // ドロワーメニュー
    $('#toggle-slidebar, .drawer-bg, #drawer-menu__close-btn').click(function() {
        $('.drawer-bg').toggleClass('open');
        $('.drawer-menu').toggleClass('open');
        return false;
    });

    // お知らせメニュー
    $('#toggle-notice, .notice-bg, #notice-menu__close-btn').click(function() {
        $('.notice-bg').toggleClass('open');
        $('.notice-menu').toggleClass('open');
        return false;
    });

    // 検索メニュー
    $('#toggle-search-slidebar, .search-bg, #search-content__close-btn').click(function() {
        $('.search-bg').toggleClass('open');
        $('.search-content').toggleClass('open');
        return false;
    });

    // アカウントメニュー
/*
    $(document).click(function() {
        $('#account-menu').removeClass('active');
    });
*/
    $('#account-menu').click(function() {
        // 閉じている状態でクリックした場合
        // メニューが閉まってしまうのを防ぐ
        //event.stopPropagation();
//        $('#account-menu-bg').toggleClass('active');
//        $('#account-menu').toggleClass('active');
//        return false;
    });
    $('#site-header__account-btn, #account-menu-bg').click(function() {
        $('#account-menu-bg').toggleClass('active');
        $('#account-menu').toggleClass('active');
        return false;
    });

    // ログインモーダル
    $('.js-modal-open').each(function(){
        $(this).on('click', function() {
            var target = $(this).data('target');
            var modal = document.getElementById(target);
            $(modal).fadeIn();
            return false;
        });
    });
    $('.js-modal-close').on('click',function(){
        $('.js-modal').fadeOut();
        return false;
    }); 

    // iPad iPhoneの判定
    var agent = window.navigator.userAgent.toLowerCase();
    var isIPad = agent.indexOf('ipad') > -1 || agent.indexOf('macintosh') > -1 && 'ontouchend' in document;
    var isIPhone = agent.indexOf('iphone') > -1;
    if (isIPad == true || isIPhone == true) {
        // LINE連携をしてもらいたいのでアプリへの誘導は削除
//        $('#app-install').show();
    }
});

