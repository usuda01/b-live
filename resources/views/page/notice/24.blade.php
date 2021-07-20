@extends('layouts.app')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お知らせ</h2>
            <div class="box notice-detail">
                <div class="section">
                    <h3>サーバーを増設いたしました</h3>
                    <div class="date">2021.02.14</div>
                    <div>サイトが重くなってきたため、サーバーを増設いたしました。</div>
                </div>
           </div>
        </div>
    </div>
@endsection
