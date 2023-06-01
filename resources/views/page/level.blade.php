@extends('layouts.app')
@section('title', 'レベルについて - ')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>レベルについて</h2>
            <div class="box">
                <div class="section">
                    <h3>1. レベルについて</h3>
                    <p>1時間視聴するごとに、レベルが1上がります。</p>
                    <p>レベル上限はありません。</p>
                </div>
                <div class="section">
                    <h3>2. 集計のタイミングについて</h3>
                    <p>毎日0時時点で、その日の視聴時間を集計します。1時間以内の場合はレベルは上がりません。</p>
                    <p>&nbsp;</p>
                    <p>毎月1日の0時時点で、レベル調整として10下がります。</p>
                </div>
           </div>
        </div>
    </div>
@endsection

