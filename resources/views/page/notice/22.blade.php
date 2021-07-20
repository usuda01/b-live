@extends('layouts.app')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お知らせ</h2>
            <div class="box notice-detail">
                <div class="section">
                    <h3>ストリームキーを固定化しました</h3>
                    <div class="date">2020.11.28</div>
                    <div>要望が多かったストリームキーの固定化を実装しました。</div>
                    <div>今後は同じストリームキーで配信が可能です。</div>
                    <div>不具合あればtwitterまでお知らせください。@BLIVE77191685</div>
                </div>
           </div>
        </div>
    </div>
@endsection
