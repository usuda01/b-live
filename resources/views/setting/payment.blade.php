@extends('layouts.app')
@section('content')
    <div class="setting-payment">
        <div class="main-content">
            <h2>お支払い手続き</h2>
            <div class="payment-form">
                <form action="/setting/payment-confirm" method="post" id="payment-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="plan1_id" value="{{ Config::get('services.stripe.plan1_id') }}">
                    <ul>
                        <li>
                            <label class="label">お支払い金額</label>
                            <div>プレミアム会員入会費（220円/月額)</div>
                        </li>
                        <li>
                            <label class="label">メールアドレス（必須）</label>
                            <input type="email" class="billing_settings_address_input" required name="email" value="{{ old('email') }}">
                        </li>
                        <li>
                            <label class="label">カード情報</label>
                            <div id="card-element" class="card-element">
                                <div id="card-number" class="stripe-input"></div>
                                <div id="card-expiry" class="stripe-input"></div>
                                <div id="card-cvc" class="stripe-input"></div>
                            </div>
                            <div id="stripe-err" class="stripe-err"></div>
                        </li>
                    </ul>
                    <input type="submit" class="submit" value="確認画面へ">
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
    createStripeForm();


    function createStripeForm() {

        // StripeのAPIキーを読み込み
        var stripe = Stripe('{{ Config::get('services.stripe.publishable_key') }}');

        // Elementsインスタンスを作成
        var elements = stripe.elements();

        // Elementsのスタイルを指定（ここはお好みで）
        var style = {
            base: {
                color: '#32325d',
                fontSize: '1rem',
//                lineHeight: '34px'
            },
            invalid: { //値が不正のときに文字色を変える
                color: '#f44336',
                iconColor: '#fa755a'
            }
        };

        // フォームのidを指定してElementsをマウント
        const cardNumber = elements.create('cardNumber', {style: style,placeholder: 'カード番号 1111 1111 1111 1111'});
        cardNumber.mount('#card-number');
        const cardExpiry = elements.create('cardExpiry',{style: style,placeholder: '有効期限　MM/YY'});
        cardExpiry.mount('#card-expiry');
        const cardCvc = elements.create('cardCvc', {style: style,placeholder: 'セキュリティ番号'});
        cardCvc.mount('#card-cvc');

        // カード番号のリアルタイムバリデーション
        cardNumber.addEventListener('change', function(event) {
            var displayError = document.getElementById('stripe-err');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Submitボタンでエラーをチェック
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            stripe.createToken(cardNumber).then(function(result) {
                if (result.error) {
                    var errorElement = document.getElementById('stripe-err');
                    errorElement.textContent = result.error.message;
                } else {
                    // Send the token to your server.
                    stripeTokenHandler(result.token);
                }
            });
        });

        // フォーム送信処理 (stripeTokenをhiddenで送信)
        function stripeTokenHandler(token) {
            // Insert the token ID into the form so it gets submitted to the server
            var form = document.getElementById('payment-form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);

            // Insert the last4 into the form so it gets submitted to the server
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'last4');
            hiddenInput.setAttribute('value', token.card.last4);
            form.appendChild(hiddenInput);

            // Submit the form
            form.submit();
            // ajaxでsubmitさせる
/*
            var params = {
                product_id: form.product_id.value,
                name: form.name.value,
                email: form.email.value,
                tel: form.tel.value,
                postal_code: form.postal_code.value,
                prefecture_id: form.prefecture_id.value,
                address1: form.address1.value,
                address2: form.address2.value,
                stripeToken: token.id
            };
            axios.post('/product/payment-confirm', params);
*/
        }
    };
    </script>
@endpush
