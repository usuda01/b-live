@extends('layouts.app')
@section('title', '配信方法' . ' - ')
@section('content')
    <div class="page-content">
        @include('parts.liver_menu')
        <div class="main-content">
            <h2>配信方法</h2>
            <div class="box howto">
                <h3>PCを使った配信方法</h3>
                <p>① OBS等の配信ソフトウェアをインストールしておく</p>
                <p>② B-LIVEにログイン後、右上の「配信する」をクリック</p>
                <p>③ 動画タイトルを入力してください。サムネイルは任意です</p>
                <p>④ 表示されているストリームサーバー、ストリームキーを、ご利用の配信ソフトウェアに設定</p>
                <h4>B-LIVE側の表示</h4>
                <p><img src="/images/page-howto01.jpg" class="capture capture01"></p>
                <h4>OBSの設定</h4>
                <p>サーバー、ストリームキーの設定</p>
                <p><img src="/images/page-howto02.jpg" class="capture"></p>
                <p>ビットレート、キーフレームの設定</p>
                <p>B-LIVEでは遅延をなるべく抑えるため、以下の値を推奨しております。</p>
                <p>ビットレート：〜3500 Kbps、キーフレーム：2</p>
                <p><img src="/images/page-howto04.jpg" class="capture"></p>
                <p>解像度：1280×720</p>
                <p>FPS：30〜60</p>
                <p><img src="/images/page-howto05.png" class="capture"></p>
                <p>⑤ 配信ソフトウェアで配信を開始した後、「<span class="color">配信開始</span>」ボタンを押すことで開始されます</p>
                <p>⑥ 配信終了時は、「配信終了」ボタンを押してください</p>
                <p><img src="/images/page-howto03.jpg" class="capture capture05"></p>


                <h3>チャットをポップアウトできます</h3>
                <p>配信画面にて、チャット欄上部のメニューを押すと、「チャットをポップアウト」が表示されます。</p>
                <p>これをクリックすることで、チャットメッセージだけのウィンドウを表示できます。OBSのコメント欄にご利用いただけます。</p>
                <p><img src="/images/page-howto06.jpg" class="capture"></p>
<!--
                <h3>スマホ画面の配信方法</h3>
                <div class="title">ゲーム配信など、スマホ画面を配信することができます。</div>
                <p>① 配信用アプリ、「Live Now」をApp Storeからダウンロード</p>
                <p>②B-LIVEにログイン後、<a href="/setting/stream"><span class="color">動画配信のページ</span></a> に移動</p>
                <p>③ サムネイル、動画タイトルを入力してください</p>
                <p>④ 表示されているストリームサーバー、ストリームキーを、「Live Now」に設定</p>
                <p>⑤ 「Live Now」でブロードキャストを開始した後、「<span class="color">配信開始</span>」ボタンを押すことで開始されます</p>
                <img src="/images/page-stream.jpg" class="capture">
-->
            </div>
        </div>
    </div>
@endsection

