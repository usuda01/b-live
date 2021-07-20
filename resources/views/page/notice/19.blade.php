@extends('layouts.app')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お知らせ</h2>
            <div class="box notice-detail">
                <div class="section">
                    <h3>２重で配信されてしまうバグを修正しました</h3>
                    <div class="date">2020.09.24</div>
                    <div>これまでご迷惑をおかけしました！</div>
                    <div>これで大丈夫なはずです！</div>
                    <div>&nbsp;</div>
                </div>
           </div>
        </div>
    </div>
@endsection
