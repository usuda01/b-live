@extends('layouts.app')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お知らせ</h2>
            <div class="box notice-detail">
                <div class="section">
                    <h3>最新の動画をダウンロードできるようにしました</h3>
                    <div class="date">2020.12.01</div>
                    <div>要望が多かったアーカイブ機能ですが、24時間以内に配信した最新の動画をmp4型式でダウンロードできるようにしました。</div>
                    <div>OBSの配信終了をした時点でアーカイブが最新の動画として作成されます。</div>
                    <div>配信中は正常にダウンロードされない可能性があります。</div>
                </div>
           </div>
        </div>
    </div>
@endsection
