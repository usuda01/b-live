@extends('layouts.app')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お知らせ</h2>
            <div class="box notice-detail">
                <div class="section">
                    <h3>３万円獲得イベント開催！</h3>
                    <div class="date">2021.06.07</div>
                    <div>
                        <div>３万円獲得イベントを開催いたします。詳細は<a href="/event">こちらから</a></div>
                    </div>
                </div>
           </div>
        </div>
    </div>
@endsection
