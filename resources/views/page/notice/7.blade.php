@extends('layouts.app')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お知らせ</h2>
            <div class="box notice-detail">
                <div class="section">
                    <h3>動画の720p再生に対応しました</h3>
                    <div class="date">2020.07.06</div>
                    <div>これまで、配信が止まってしまうという現象が起こっておりました。</div>
                    <div>&nbsp;</div>
                    <div>サーバーの配信周りを見直して、720pまでの配信に対応いたしました。</div>
                </div>
           </div>
        </div>
    </div>
@endsection
