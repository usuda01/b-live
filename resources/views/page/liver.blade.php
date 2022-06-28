@extends('layouts.app')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>配信者の方へ</h2>
            <div class="box">
                <div class="section">
                    <h3>1. 配信人数制限について</h3>
                    <p>現在、同時配信は{{ Config::get('services.max_liver') }}人までに制限しております。</p>
                </div>
                <div class="section">
                    <h3>2. 配信時間について</h3>
                    <p>１回の配信につき、4時間まで可能といたします。</p>
                    <p>配信時間を過ぎた場合、配信が自動的に終了します。</p>
                </div>
                <div class="section">
                    <h3>3. 同時配信について</h3>
                    <p>当サービスにおいては許可するものといたしますが、他のサービスで禁止されている場合はお控えください。</p>
                    <p>同時配信する場合は、B-LIVEの紹介を入れてください。</p>
                </div>
                <div class="section">
                    <h3>4. 動画のアーカイブについて</h3>
                    <p>アーカイブ動画は残りません。</p>
<!--
                    <p>過去24時間以内に配信した最新の動画をmp4型式でダウンロードできます。
                    <p>OBSの配信終了をした時点でアーカイブが最新の動画として作成されます。</p>
                    <p>配信中は正常にダウンロードされない可能性があります。</p>
                    <p>その他のアーカイブ動画は残りませんが、配信履歴およびメッセージは3ヶ月残ります。</p>
-->
                </div>
           </div>
        </div>
    </div>
@endsection

