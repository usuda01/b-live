@extends('layouts.app')
@section('content')
    <div class="setting-payment">
        <div class="main-content">
            <h2>お支払いの確認</h2>
            <div class="payment-form">
                <form action="/setting/payment-exec" method="post" id="payment-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="plan1_id" value="{{ $input['plan1_id'] }}">
                    <input type="hidden" name="stripeToken" value="{{ $input['stripeToken'] }}">
                    <input type="hidden" name="email" value="{{ $input['email'] }}">
                    <ul>
                        <li>
                            <label class="label">お支払い金額</label>
                            <div>プレミアム会員入会費（220円/月額)</div>
                        </li>
                        <li>
                            <label class="label">メールアドレス（必須）</label>
                            <div>{{ $input['email'] }}</div>
                        </li>
                        <li>
                            <label class="label">カード情報</label>
                            <div class="">**** **** **** {{ $input['last4'] }}</div>
                        </li>
                    </ul>
                    <button type="submit" name="action" value="back" class="back">修正する</button>
                    <input type="submit" name="action" class="submit" value="上記内容で会員になる">
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
