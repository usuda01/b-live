@extends('layouts.app')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お知らせ</h2>
            <div class="box notice-detail">
                <div class="section">
                    <h3>LINE通知機能を作ったよ！</h3>
                    <div class="date">2021.07.01</div>
                    <div>
                        <div>「Androidアプリはないのか？」という声が多く寄せられます。</div>
                        <div>しかしながら、開発言語がWEBとは全く異なるためなかなか簡単にはいかず、配信通知はiPhoneユーザーに限られていました。。。</div>
                        <div>そこで、今回はLINEでの通知を可能にしました。</div>
                        <div>詳細は<a href="/setting/line">こちら</a>から！</div>
                    </div>
                </div>
           </div>
        </div>
    </div>
@endsection
