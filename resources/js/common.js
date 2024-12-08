// アドレスバーの高さを取得する関数
function getAddressBarHeight() {
    const addressBarHeight = window.outerHeight - window.innerHeight;
    document.documentElement.style.setProperty('--address-bar-height', `${addressBarHeight}px`);
}

// 初期化時にアドレスバーの高さを設定する
getAddressBarHeight();

// アドレスバーの高さが変わった時に、ビデオの高さを再設定する
window.addEventListener('resize', getAddressBarHeight);

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

    // お知らせのクリックイベント
    $('#notice-menu__list').on('click', 'li', function (e) {
        const $notificationItem = $(this);
        const notificationId = $notificationItem.data('id');

        if (!notificationId) {
            console.error('Notification ID is missing.');
            return;
        }

        $.ajax({
            url: `/api/notifications/mark-as-read`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': `Bearer ${window.Laravel.apiToken}`
            },
            data: {
                notification_id: notificationId // データとしてIDを送信
            },
            success: function (response) {
                if (response.message === 'Notification marked as read.') {

                }
            },
            error: function (xhr) {
                console.error('Error marking notification as read:', xhr.responseText);
            }
        });
    });

    // LINE通知の広告非表示
    $('#line-connect #line-connect-btn-close').click(function() {
        $('#line-connect').hide();
    });

    // ログインモーダル
    $('.js-modal-open').each(function() {
        $(this).on('click', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            var modal = document.getElementById(target);
            $(modal).fadeIn();
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

