@extends('layouts.app')
@section('content')
    <div class="search-index">
        @include('parts.home_menu')
        <div class="main-area">
            <h2 class="main-title">"{{ $q }}"の検索結果</h2>
            <search-component
                :q="{{ json_encode($q) }}"
            ></search-component>
        </div>
    </div>
@endsection
