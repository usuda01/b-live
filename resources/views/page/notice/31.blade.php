@extends('layouts.app')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お知らせ</h2>
            <div class="box notice-detail">
                <div class="section">
                    <h3>サーバーを移管しました！</h3>
                    <div class="date">2021.08.31</div>
                    <div>
                        <div>これまで、シンガポールのサーバーを利用していましたが、通信が途中で途絶え、正常に配信が行えない事象が発生していました。</div>
                        <div>国内サーバーに移管したので、前よりは安定したと思います。</div>
                        <div>配信先のURLが変更となりましたので、配信者の方はご注意ください。</div>
                    </div>
                </div>
           </div>
        </div>
    </div>
@endsection
