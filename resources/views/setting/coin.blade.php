@extends('layouts.app')
@section('content')
    <div class="setting-coin">
        @include('parts.account_menu')
        <div class="main-content">
            <h2>所持コイン</h2>
            <div class="coin-form">
                @if (session('flash_message'))
                    <div class="flash_message">
                        {{ session('flash_message') }}
                    </div>
                @endif
                @if ($errors->any())
                    <ul class="error">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    </ul>
                @endif
                <ul>
                    <li>
                        <div class="coin"><img src="/images/icon-coin.png"><div class="point">{{ $user->user_data->point }} コイン</div></div>
                    </li>
                </ul>
                @if ($user->user_data->point < 1000)
                    <div class="note">※1000コインから申請できます</div>
                @else
                    <a class="btn-request btn-default" href="/setting/coin-request">換金申請へ</a>
                @endif
                @if ($user->point_requests->count() > 0)
                    <div class="request-list">
                        <h3>申請済みリクエスト</h3>
                        @foreach ($user->point_requests as $point_request)
                            <ul>
                                <li><span>{{ $point_request->created_at->format('Y/m/d') }}</span><span>{{ $point_request->request_point }}&nbsp;コイン</span><span>&yen;{{ $point_request->amount }}</span></li>
                            </ul>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script src="{{ mix('js/profile.js') }}"></script>
@endpush
