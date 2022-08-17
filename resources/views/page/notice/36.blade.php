@extends('layouts.app')
@section('title', 'ショート動画にコメントできる機能を追加しました' . ' - ')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お知らせ</h2>
            <div class="box notice-detail">
                <div class="section">
                    <h3>ショート動画にコメントできる機能を追加しました</h3>
                    <div class="date">2022.08.17</div>
                    <div>
                        <div>コメントを投稿するにはログインが必要です。</div>
                        <div>動画の所有者、コメントの投稿者はコメントを削除出来ます。</div>
                    </div>
                </div>
           </div>
        </div>
    </div>
@endsection
