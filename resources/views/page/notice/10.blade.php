@extends('layouts.app')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お知らせ</h2>
            <div class="box notice-detail">
                <div class="section">
                    <h3>ブラウザからもギフトを送れるようになりました</h3>
                    <div class="date">2020.07.23</div>
                    <div>これまで、iOSしかギフトメッセージを対応しておりませんでしたが、ブラウザからもギフトを送れるようになりました。</div>
                    <div>&nbsp;</div>
                    <div>また、ギフトの金額も小額から送れるように調整しました。</div>
                    <div>10コイン</div>
                    <div>100コイン</div>
                    <div>200コイン</div>
                    <div>500コイン</div>
                    <div>1000コイン</div>
                    <div>2000コイン</div>
                    <div>&nbsp;</div>
                    <div>これまで、iPhoneアプリを使用していた方は、再インストールをお願いいたします。</div>
                </div>
           </div>
        </div>
    </div>
@endsection
