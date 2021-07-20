@extends('layouts.app')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お知らせ</h2>
            <div class="box notice-detail">
                <div class="section">
                    <h3>動画のランキング制度を実装しました</h3>
                    <div class="date">2020.07.08</div>
                    <div>各動画ごとに、同時視聴者数にもとづくランキング制度を実装しました。</div>
                    <div>&nbsp;</div>
                    <div>詳しくは、「ランキングについて」をご確認ください。</div>
                </div>
           </div>
        </div>
    </div>
@endsection
