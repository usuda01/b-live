@extends('layouts.app')
@section('title', '視聴レベル機能を追加しました！' . ' - ')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お知らせ</h2>
            <div class="box notice-detail">
                <div class="section">
                    <h3>視聴レベル機能を追加しました！</h3>
                    <div class="date">2023.06.01</div>
                    <div>
                        <div>詳細は<a href="/page/level">こちらから</a></div>
                    </div>
                </div>
           </div>
        </div>
    </div>
@endsection
