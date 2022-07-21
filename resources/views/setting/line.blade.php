@extends('layouts.app')
@section('title', 'LINE連携 - ')
@section('content')
    <div class="setting-line">
        @include('parts.account_menu')
        <div class="main-content">
            <h2>LINE連携</h2>
            <div class="line-content">
                <div class="line-header">
                    <div class="header-inner">
                        <div class="icon"><img src="/images/icon-line.png"></div>
                        <div class="txt">
                            <div class="main-text">配信開始を<br class="tb_only">LINEで通知!</div>
                            <div class="sub-text pc_only">フォローした配信者の配信開始をLINEでお知らせします！見逃しを無くそう！</div>
                        </div>
                    </div>
                    <div class="sub-text tb_only">フォローした配信者の配信開始をLINEでお知らせします！見逃しを無くそう！</div>
                </div><!-- // .line-header -->


                <div class="line-flow">
                    <div class="midashi">◆ご利用方法</div>
                    <div class="flow-content">
                        <div class="flow flow1">
                            <div class="step">STEP 1</div>
                            <div class="txt pc_only">スマートフォンで以下のQRコードを読み取ってください。 LINEアプリが開きます。</div>
                            <div class="txt tb_only">以下の「友だち登録」ボタンををクリックしてください。<br>LINEアプリが開きます。</div>
                            <div class="pc_only">
                                <div class="friend">友達登録</div>
                                <img id="line-qr" class="line-qr" src="" data-user="{{ Auth::user()->id }}">
                            </div>
                            <div class="tb_only"><a id="line-submit" class="line-submit" data-user="{{ Auth::user()->id }}" href="https://dummy.com">友だち登録</a></div>
                        </div>
                        <div class="pc_only">
                            <div class="arrow"></div>
                        </div>
                        <div class="flow flow2">
                            <div class="step">STEP 2</div>
                            <div class="txt">「追加」をタップしてください。</div>
                            <div class="image"><img src="/images/line-step2.jpg"></div>
                        </div>
                        <div class="pc_only">
                            <div class="arrow"></div>
                        </div>
                        <div class="flow flow3">
                            <div class="step">STEP 3</div>
                            <div class="txt">
                                トーク画面が開いたら、そのまま「送信」をタップしてください。
                                <div class="sub-txt tb_only">※入力欄には自動で【連携ID】が入力されていますので、そのまま送信してください。</div>
                            </div>
                            <div class="image"><img src="/images/line-step3.jpg"></div>
                            <div class="sub-txt pc_only">※入力欄には自動で【連携ID】が入力されていますので、そのまま送信してください。</div>
                        </div>
                        <div class="pc_only">
                            <div class="arrow"></div>
                        </div>
                        <div class="flow flow4">
                            <div class="step">完了</div>
                            <div class="txt">これで登録完了です。フォローした配信者の通知を受け取れます。</div>
                            <div class="image"><img src="/images/line-step4.jpg"></div>
                        </div>
                    </div><!--// .flow-content -->
                </div><!--// .line-flow -->
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script src="{{ mix('js/profile.js') }}"></script>
@endpush
