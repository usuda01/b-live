@extends('layouts.app')
@section('title', 'フォロワーにメールで配信開始をお知らせする機能を追加しました！' . ' - ')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お知らせ</h2>
            <div class="box notice-detail">
                <div class="section">
                    <h3>フォロワーにメールで配信開始をお知らせする機能を追加しました！</h3>
                    <div class="date">2023.01.27</div>
                    <div>
                        <div>フォロワーにメールで配信開始をお知らせする機能を追加しました！</div>
                        <div>配信開始と同時に、フォロワーに一斉にメールが配信されます。</div>
                        <div>メールを受け取りたくない方は、アカウントの通知設定から「通知しない」を設定することで解除できます。</div>
                    </div>
                </div>
           </div>
        </div>
    </div>
@endsection
