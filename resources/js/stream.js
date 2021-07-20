function copyToClipboard(copyTarget) {
    // コピー対象のテキストを選択する
    copyTarget.select();
    document.execCommand('Copy');
}

$(function() {
    $('#file-01').change(function(){
        $('#mask-file-01').text($('#file-01').val());
    });
    $('.file-mask').click(function(){
        $('#file-01').click();
    });

    // 動画プレビュー
/*
    var player = videojs('main-video', {
        autoplay: true,
        fluid: true,
    });
*/

    $('#btn-server-url-copy').click(function() {
        copyToClipboard(document.getElementById('server-url'));
    });
    $('#btn-stream-key-copy').click(function() {
        copyToClipboard(document.getElementById('stream-key'));
    });
});
