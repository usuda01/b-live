@extends('layouts.app')
@section('title', '画質調整機能を実装しました！' . ' - ')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お知らせ</h2>
            <div class="box notice-detail">
                <div class="section">
                    <h3>画質調整機能を実装しました！</h3>
                    <div class="date">2024.10.30</div>
                    <div>
                        <div>画質調整機能を実装しました！</div>
                        <div>視聴画面の、「ギアマーク」をクリックすることで画質調整ができます。</div>
                    </div>
                </div>
           </div>
        </div>
    </div>
@endsection
