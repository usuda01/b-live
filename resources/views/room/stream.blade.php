@extends('layouts.app')

@section('title', $room->name . ' - ')

@section('content')
    <room-component
        :admin-user-id="{{ Config::get('services.admin_user_id') }}"
        :room="{{ $room->toJson() }}"
        :user="{{ ($user) ? $user->toJson() : "{}" }}"
    >
    </room-component>
@endsection

@push('meta')
    <meta property="og:image" content="{{ Request::getSchemeAndHttpHost() }}/{{ $room->image_path }}" />
    <meta property="twitter:image" content="{{ Request::getSchemeAndHttpHost() }}/{{ $room->image_path }}" />
@endpush

@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ mix('js/room.js') }}"></script>
    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
@endpush
