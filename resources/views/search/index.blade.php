@extends('layouts.app')
@section('content')
    <div class="search-index">
        @include('parts.home_menu')
        <div class="main-area">
            <h2 class="main-title">{{ $q }}</h2>
            <search-component
                :active-tab="{{ $activeTab }}"
                :q="{{ json_encode($q) }}"
            ></search-component>
        </div>
    </div>
@endsection
