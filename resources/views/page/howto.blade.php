@extends('layouts.app')
@section('title', '配信方法(PC)' . ' - ')
@section('content')
    <div class="page-content">
        @include('parts.liver_menu')
        <div class="main-content">
            <h2>配信方法(PC)</h2>
            <div class="box howto">
                <h3>PCを使った配信方法</h3>

                <div class="section">
                    <h4>配信方法</h4>
                    <p>① OBS等の配信ソフトウェアをインストールしておく</p>
                    <p>② B-LIVEにログイン後、右上の「配信する」をクリック</p>
                    <p>③ 動画タイトルを入力してください。サムネイルは任意です</p>
                    <p>④ 表示されているストリームサーバー、ストリームキーを、ご利用の配信ソフトウェアに設定</p>
                    <div class="midashi">B-LIVE側の表示</div>
                    <div class="capture capture01"><a href="/images/page-howto01.jpg" target="_blank"><img src="/images/page-howto01.jpg"></a></div>
                    <div class="midashi">OBSの設定</div>
                    <div class="sub-midashi">サーバー、ストリームキーの設定</div>
                    <div class="capture"><a href="/images/page-howto02.jpg" target="_blank"><img src="/images/page-howto02.jpg"></a></div>
                    <div class="sub-midashi">ビットレート、キーフレームの設定</div>
                    <p>B-LIVEでは遅延を抑えるため、以下の値を推奨しております。</p>
                    <p>ビットレート：〜3500 Kbps</p>
                    <p>キーフレーム：2</p>
                    <div class="capture"><a href="/images/page-howto04.jpg" target="_blank"><img src="/images/page-howto04.jpg"></a></div>
                    <div class="sub-midashi">解像度、FPSの設定</div>
                    <p>解像度：1280×720</p>
                    <p>FPS：30〜60</p>
                    <div class="capture"><a href="/images/page-howto05.png" target="_blank"><img src="/images/page-howto05.png"></a></div>
                    <p>⑤ 配信ソフトウェアで配信を開始した後、「配信開始」ボタンを押すことで開始されます</p>
                    <p>⑥ 配信終了時は、「配信終了」ボタンを押してください</p>
                    <div class="capture capture05"><a href="/images/page-howto03.jpg" target="_blank"><img src="/images/page-howto03.jpg"></a></div>
                </div>

                <div class="section">
                    <h4>コメントビューアーについて</h4>
                    <p>Ryu氏開発の「マルチコメントビューアー」を「B-LIVE」に対応させました。</p>
                    <p>ソースコードは<a class="color" href="https://www.carol-i.com/wp-content/uploads/2024/10/MultiCommentViewer_v0.6.34_stable.zip">こちらから</a>ダウンロード出来ます。</p>
                    <p>※「B-LIVE」用に改造したものですので、非公式のものとなります。</p>
                </div>

                <div class="section">
                    <h4>チャットをポップアウトできます</h4>
                    <p>配信画面にて、チャット欄上部のメニューを押すと、「チャットをポップアウト」が表示されます。</p>
                    <p>これをクリックすることで、チャットメッセージだけのウィンドウを表示できます。</p>
                    <p>OBSのコメント欄にご利用いただけます。</p>
                    <div class="capture"><a href="/images/page-howto06.jpg" target="_blank"><img src="/images/page-howto06.jpg"></a></div>
                </div>
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

