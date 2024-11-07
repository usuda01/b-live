@extends('layouts.app')
@section('title', '配信者特典 - ')
@section('content')
    <div class="page-content">
        @include('parts.liver_menu')
        <div class="main-content">
            <h2>配信者特典</h2>
            <div class="box">
                <div class="section">
                    <h3>配信者特典</h3>
                </div>
                <div class="section">
                    <h3>ギフトメッセージ</h3>
                    <p>所持コインの30%を還元いたします（1コイン = 1円）</p>
                    <p>所持コインが、1,000コイン以上となった場合、換金の申請ができます。</p>
                    <p>振り込み手数料はこちらで負担します。</p>
                    <p>&nbsp;</p>
                    <p>【注意事項】</p>
                    <p>⚫︎一度に配信できる時間は{{ config('services.max_stream_time') }}時間までとします</p>
                    <p>⚫︎時給制ではありません</p>
                </div>
                <div class="section">
                    <h3>お支払い方法</h3>
                    <p>翌月15日までに銀行振り込みをいたします。</p>
                    <p>お問い合わせページより、サイト上のお名前と、お振込先をご連絡ください。</p>
                    <p>お振込先情報をお伝えいただけない場合、お支払いは出来ません。</p>
                </div>
           </div>
        </div>
    </div>
@endsection

