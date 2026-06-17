<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { background: transparent; }
        body { font-family: -apple-system, "Hiragino Sans", sans-serif; }
        .app-notice {
            display: block;
            width: 100%;
            background: #faf5fc;
            border: 1px solid #e6d4ec;
            border-radius: 14px;
            padding: 16px 18px;
            text-decoration: none;
            color: #4a2553;
        }
        .app-notice-label {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            color: #fff;
            background: #9e1b9e;
            border-radius: 999px;
            padding: 3px 12px;
            margin-bottom: 8px;
        }
        .app-notice-title { display: block; font-size: 15px; font-weight: 700; line-height: 1.4; color: #4a2553; }
        .app-notice-text { display: block; font-size: 13px; line-height: 1.5; color: #6b5170; margin-top: 4px; }
    </style>
</head>
<body>
    @php
        // お知らせ：表示ON/OFFと内容をこのファイルで編集する（編集後に本番へ git pull でデプロイ）。
        $showNotice = true;                  // false にすると非表示
        $noticeTitle = '2026年7月1日より利用規約を改定します';
        $noticeText = '録画した映像をライブ配信として配信する行為を、禁止事項に追加します';
        $noticeUrl = url('/page/notice/50');  // タップで開くページ。リンク不要なら null にする
    @endphp
    @if ($showNotice)
        @php $noticeTag = $noticeUrl ? 'a' : 'div'; @endphp
        <{{ $noticeTag }} class="app-notice" @if ($noticeUrl) href="{{ $noticeUrl }}" @endif>
            <span class="app-notice-label">お知らせ</span>
            <span class="app-notice-title">{{ $noticeTitle }}</span>
            @if ($noticeText)
                <span class="app-notice-text">{{ $noticeText }}</span>
            @endif
        </{{ $noticeTag }}>
    @endif
</body>
</html>
