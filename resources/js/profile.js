$(function() {
    $('#file-01').change(function() {
        $('#mask-file-01').text($('#file-01').val());
    });
    $('.file-mask').click(function() {
        $('#file-01').click();
    });

    // LINEのURL生成
    let userId = $('#line-qr').attr('data-user');
    let message = '【連携ID】F';
        message += userId + '6C\n';
        message += 'このまま送信してね！';
        message = encodeURIComponent(message);
    let url = 'https://chart.apis.google.com/chart?cht=qr&chs=300x300&choe=Shift_JIS&chl=';
        url += 'https://line.me/R/oaMessage/@227vfrpo/?';
        url += message;
    $('#line-qr').attr('src', url);
    $('#line-submit').on('click',function() {
        let userId = $(this).attr('data-user');
        let message = '【連携ID】F';
            message += userId + '6C\n';
            message += 'このまま送信してね！';
            message = encodeURIComponent(message);
        location.href = 'https://line.me/R/oaMessage/@227vfrpo/?' + message
        return false;
    });
});
