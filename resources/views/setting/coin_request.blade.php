@extends('layouts.app')
@section('content')
    <div class="setting-coin-request">
        @include('parts.account_menu')
        <div class="main-content">
            <h2>換金申請</h2>
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
                <form method="post" accept-charset="utf-8" class="" action="/setting/coin-request">
                    @csrf
                    <ul>
                        <li>
                            <label class="label">所持コイン</label>
                            <div class="coin"><img src="/images/icon-coin.png"><div class="point">{{ $user->user_data->point }} コイン</div></div>
                        </li>
                        <li>
                            <label class="label">申請コイン（換金率{{ config('services.point_conversion_rate') }}%）</label>
                            <input type="text" name="request_point" value="{{ old('request_point') }}" placeholder="{{ $user->user_data->point }}">
                        </li>
                    </ul>
                    <ul class="bank-info">
                        <li>
                            <label class="label">銀行名</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name') }}" placeholder="三井住友銀行">
                        </li>
                        <li>
                            <label class="label">支店名</label>
                            <input type="text" name="branch_name" value="{{ old('branch_name') }}" placeholder="渋谷駅前支店">
                        </li>
                        <li class="account-type">
                            <label class="label">口座種別</label>
                            <label class="account-type-label"><input type="radio" name="account_type" value="1" {{ old('account_type') == 1 ? 'checked' : '' }}>普通</label>
                            <label class="account-type-label"><input type="radio" name="account_type" value="2" {{ old('account_type') == 2 ? 'checked' : '' }}>当座</label>
                            <label class="account-type-label"><input type="radio" name="account_type" value="3" {{ old('account_type') == 3 ? 'checked' : '' }}>定期</label>
                        </li>
                        <li>
                            <label class="label">口座番号</label>
                            <input type="text" name="account_number" value="{{ old('account_number') }}" placeholder="1234567">
                        </li>
                        <li>
                            <label class="label">口座名義（漢字）</label>
                            <input type="text" name="account_name" value="{{ old('account_name') }}" placeholder="田中太郎">
                        </li>
                    </ul>
                    <button type="submit" class="btn-default">申請リクエスト</button>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script src="{{ mix('js/profile.js') }}"></script>
@endpush
