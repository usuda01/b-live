@extends('layouts.app')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お知らせ</h2>
            <div class="box notice-detail">
                <div class="section">
                    <h3>利用規約を変更いたしました。</h3>
                    <div class="date">2020.05.31</div>
                    <div>動画のアーカイブは2週間まで保存します。</div>
                    <div>2週間経過後、動画の視聴はできなくなりますが、コメントや発生した時給は残ります。</div>
                </div>
           </div>
        </div>
    </div>
@endsection

