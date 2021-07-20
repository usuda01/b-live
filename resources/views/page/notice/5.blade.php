@extends('layouts.app')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お知らせ</h2>
            <div class="box notice-detail">
                <div class="section">
                    <h3>サービスを再開しました。</h3>
                    <div class="date">2020.06.24</div>
                    <div>この度、サーバーの増強をし、サービスの再開をいたしました。</div>
                    <div>&nbsp;</div>
                    <div>配信時間や同時配信者数に制限がありますので、<a href="/page/liver/">配信者の方へ</a>をご一読ください。</div>
                </div>
           </div>
        </div>
    </div>
@endsection
