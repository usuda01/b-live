@extends('layouts.app')
@section('title', '配信方法(iOS) - ')
@section('content')
    <div class="page-content">
        @include('parts.liver_menu')
        <div class="main-content">
            <h2>配信方法(iOS)</h2>
            <div class="box howto">
                <h3>B-LIVE 配信アプリ（iOS）</h3>
                <p>iPhone / iPad の画面をそのままライブ配信できる、B-LIVE 公式の配信アプリです。ゲーム画面や他のアプリの画面もキャプチャして配信できます。</p>

                <div class="section">
                    <h4>1. アプリをインストール</h4>
                    <p>まだアプリをお持ちでない方は、まずApp Storeから「B-LIVE 配信アプリ」をインストールしてください。</p>
                    <p><a href="https://apps.apple.com/jp/app/b-live-%E9%85%8D%E4%BF%A1-%E3%82%B2%E3%83%BC%E3%83%A0%E5%AE%9F%E6%B3%81%E9%85%8D%E4%BF%A1%E3%83%84%E3%83%BC%E3%83%AB/id6760190440" target="_blank"><img src="/images/app-store-badge-jp.svg" alt="App Storeからダウンロード" style="height:50px;"></a></p>
                </div>

                <div class="section">
                    <h4>2. 配信を始める</h4>
                    <p>① ホーム画面下部の「配信を開始」ボタンをタップします。</p>
                    <div class="capture"><a href="/images/page-howto-ios-01.png" target="_blank"><img src="/images/page-howto-ios-01.png"></a></div>

                    <p>② 配信タイトルを入力し、サムネイル画像と画面の向きを選択します。<br>※ 画面の向きは配信開始後に変更できません。</p>
                    <p>③ 「配信を準備する」ボタンをタップします。</p>

                    <p>④ 「配信の準備ができました」と表示されたら、画面に出る録画ボタンをタップします。</p>

                    <p>⑤ 表示されるシートから「B-LIVE」を選択し、「ブロードキャストを開始」をタップします。</p>
                    <div class="capture"><a href="/images/page-howto-ios-02.png" target="_blank"><img src="/images/page-howto-ios-02.png"></a></div>

                    <p>⑥ 配信が始まると、ホーム画面に「配信中」のバナーと視聴用URLが表示されます。</p>
                    <div class="capture"><a href="/images/page-howto-ios-03.png" target="_blank"><img src="/images/page-howto-ios-03.png"></a></div>
                </div>

                <div class="section">
                    <h4>3. 配信中の操作</h4>

                    <div class="sub-midashi">コメントを画面に表示する（PiP）</div>
                    <p>ホーム画面の「コメントを表示する」ボタンをタップすると、ピクチャ・イン・ピクチャ（小窓）で視聴者のコメントが画面に表示されます。配信画面の上に重ねて表示できるので、ゲームをしながらでもコメントを確認できます。</p>
                    <div class="capture"><a href="/images/page-howto-ios-04.png" target="_blank"><img src="/images/page-howto-ios-04.png"></a></div>

                    <div class="sub-midashi">コメント一覧を見る</div>
                    <p>「コメント一覧」ボタンから、これまでのコメントをまとめて見ることができます。</p>

                    <div class="sub-midashi">コメントを音声で読み上げる</div>
                    <p>「コメント読み上げ」をオンにすると、視聴者からのコメントを自動で音声読み上げします。読み上げの速度・音量も調整できます。</p>
                </div>

                <div class="section">
                    <h4>4. 配信を終了する</h4>
                    <p>配信を終了する方法は2通りあります。</p>

                    <div class="sub-midashi">方法A: アプリ内の「配信を終了」ボタン</div>
                    <p>① ホーム画面下部の赤い「配信を終了」ボタンをタップします。</p>
                    <p>② 確認ダイアログで「終了する」をタップします。</p>

                    <p>③ iOSから「配信を終了しました」のダイアログが出たら「OK」をタップします。</p>

                    <div class="sub-midashi">方法B: コントロールセンターから終了</div>
                    <p>① 画面の右上から下にスワイプしてコントロールセンターを開きます。</p>
                    <p>② 赤い録画アイコンをタップして画面収録を停止します。</p>
                </div>
            </div>
        </div>
    </div>
@endsection
