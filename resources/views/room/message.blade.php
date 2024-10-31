@extends('layouts.api')


@section('content')
    <room-component
        :admin-user-id="{{ Config::get('services.admin_user_id') }}"
        :is-app="{{ ($isApp) ? 'true' : 'false' }}"
        :room="{{ $room->toJson() }}"
        :listeners="{{ $listeners }}"
        :user="{{ ($user) ? $user->toJson() : "{}" }}"
        :store-interval-time="{{ Config::get('services.store_interval_time') }}"
        :wowza-ssl-host-name="'{{ Config::get('services.wowza.ssl_host_name') }}'"
    >
    </room-component>
@endsection


@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ mix('js/room.js') }}"></script>
    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
@endpush
