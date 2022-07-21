@extends('layouts.app')
@section('title', 'お問い合わせ - ')
@section('content')
    <div class="page-content">
        @include('parts.page_menu')
        <div class="main-content">
            <h2>お問い合わせ</h2>
            <div class="box contact">
                <form method="POST" action="/contact">
                    {{ csrf_field() }}
                    <ul> 
                        <li>
                            <label class="label" for="formInputName">お名前</label>
                            <input type="text" class="" id="formInputName" name="name" value="{{ old('name') }}">
         
                            @if ($errors->has('name'))
                                <div class="error-text">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </div>
                            @endif
                        </li>
         
                        <li>
                            <label class="label" for="formInputEmail">ご連絡先メールアドレス</label>
                            <input type="text" class="" id="formInputEmail" name="email" value="{{ old('email') }}">
         
                            @if ($errors->has('email'))
                                <div class="error-text">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </div>
                            @endif
                        </li>
         
                        <li>
                            <label class="label" for="formInputEmail">お問い合わせ内容</label>
                            <textarea class="" id="formInputMessage" name="message" value="{{ old('message') }}">{{ old('message') }}</textarea>
         
                            @if ($errors->has('message'))
                                <div class="error-text">
                                    <strong>{{ $errors->first('message') }}</strong>
                                </div>
                            @endif
                        </li>
         
                        <button type="submit" class="btn-default">送信</button>
                    </ul>
                </form>
            </div>
        </div>
    </div>
@endsection

