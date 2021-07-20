@extends('layouts.app')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お知らせ</h2>
            <div class="box notice-detail">
                <div class="section">
                    <h3>配信が不安定な件につきまして</h3>
                    <div class="date">2020.07.01</div>
                    <div>現在、特定のユーザーで、配信が止まってしまうという現象が起こっております。</div>
                    <div>&nbsp;</div>
                    <div>配信周りについて、現在調整中です。</div>
                    <div>&nbsp;</div>
                    <div>ご不便をおかけして申し訳ございません。</div>
                </div>
           </div>
        </div>
    </div>
@endsection
