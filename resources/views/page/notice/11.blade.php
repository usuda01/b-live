@extends('layouts.app')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お知らせ</h2>
            <div class="box notice-detail">
                <div class="section">
                    <h3>換金申請の画面を作成しました</h3>
                    <div class="date">2020.08.04</div>
                    <div>これまで、振り込み口座についてDMやメールにてやりとりを行っておりましたが、セキュリティ強化のため、申請用の画面を作成致しました。</div>
                    <div>&nbsp;</div>
                    <div>所持コインが1000コインを超えたユーザーは、アカウント＞所持コインに「換金申請へ」のボタンが表示されます。</div>
                </div>
           </div>
        </div>
    </div>
@endsection
