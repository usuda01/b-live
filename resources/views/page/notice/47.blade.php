@extends('layouts.app')
@section('title', '最大配信時間を伸ばしました！' . ' - ')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お知らせ</h2>
            <div class="box notice-detail">
                <div class="section">
                    <h3>最大配信時間を伸ばしました！</h3>
                    <div class="date">2024.11.07</div>
                    <div>
                        <div>最大配信時間を伸ばしました！</div>
                        <div>これまで、「4時間」までの配信制限としていましたが、「{{ config('services.max_stream_time') }}時間」に伸ばしました。</div>
                        <div>やったことない調整なので、バグったらごめんね。</div>
                    </div>
                </div>
           </div>
        </div>
    </div>
@endsection
