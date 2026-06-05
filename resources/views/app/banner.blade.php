<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { background: transparent; }
        .app-banner { display: block; width: 100%; }
        .app-banner img { display: block; width: 100%; height: auto; }
    </style>
</head>
<body>
    @php
        // 表示中のバナーをここで出し分ける（アプリ再申請なしで変更可能）。
        $now = date('Y-m-d H:i:s');
        $showEvent15 = config('services.event15.is_active')
            && $now <= config('services.event15.end_date');
    @endphp
    @if ($showEvent15)
        <a class="app-banner" href="{{ url('/event15') }}">
            <img src="{{ url('/images/event15/fv.jpg') }}" alt="イベント開催中">
        </a>
    @endif
</body>
</html>
