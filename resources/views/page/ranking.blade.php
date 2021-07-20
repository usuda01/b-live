@extends('layouts.app')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>ランキングについて</h2>
            <div class="box">
                <div class="section">
                    <h3>1. ランキングについて</h3>
                    <p>配信中の同時視聴者数にもとづき、動画ごとに以下のようにランク付けを行います。</p>
                    <p>&nbsp;</p>
                    <p>Aランク：100人〜</p>
                    <p>Bランク：50〜99人</p>
                    <p>Cランク：10〜49人</p>
                    <p>Dランク：〜9人</p>
                </div>
                <div class="section">
                    <h3>2. ランキング参加方法</h3>
                    <p>ログイン後、アカウントメニューから「参加する」に設定してください。</p>
                    <p>初期設定は、「参加しない」となっております。</p>
                </div>
                <div class="section">
                    <h3>3. 集計のタイミングについて</h3>
                    <p>当月分については、リアルタイムで集計されます。</p>
                    <p>毎月1日の0時時点で、当月分のランキングを集計し、該当月のランキング確定します。このとき、「参加する」を設定していたユーザーの動画は全てランキングの対象となり、後から変更してもランキングから外すことは出来ません。</p>
                </div>
           </div>
        </div>
    </div>
@endsection

