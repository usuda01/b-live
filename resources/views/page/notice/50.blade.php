@extends('layouts.app')
@section('title', '利用規約改定のお知らせ' . ' - ')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お知らせ</h2>
            <div class="box notice-detail">
                <div class="section">
                    <h3>利用規約改定のお知らせ</h3>
                    <div class="date">2026.06.16</div>
                    <div>
                        <div>2026年7月1日より、利用規約の禁止事項に以下を追加いたします。</div>
                        <div>・録画した映像を、ライブ配信として配信する行為</div>
                        <div>詳しくは<a href="/page/terms">利用規約</a>をご確認ください。</div>
                    </div>
                </div>
           </div>
        </div>
    </div>
@endsection
