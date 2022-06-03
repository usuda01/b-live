@extends('layouts.app')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お知らせ</h2>
            <div class="box notice-detail">
                <div class="section">
                    <h3>ゲームタイトルからの検索を可能にしました！</h3>
                    <div class="date">2021.08.05</div>
                    <div>
                        <div>トップページに、「人気のタイトル」という形でゲームタイトルごとに検索を出来るようにしました。</div>
                        <div>配信者の方は、配信時にゲームタイトルを選択いただければと思います。</div>
                        <div>現在6タイトルしか選択肢がありませんが、配信状況をみて随時追加していく予定です。</div>
                    </div>
                </div>
           </div>
        </div>
    </div>
@endsection
