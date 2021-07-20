@extends('layouts.api')


@section('content')
    <room-message-component
        :admin-user-id="{{ Config::get('services.admin_user_id') }}"
        :room="{{ $room->toJson() }}"
        :user="{{ ($user) ? $user->toJson() : "{}"}}">
    </room-message-component>
@endsection


@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ mix('js/room.js') }}"></script>
    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
@endpush
