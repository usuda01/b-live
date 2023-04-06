@extends('layouts.api')


@section('content')
    <room-message-viewer-component
        :admin-user-id="{{ Config::get('services.admin_user_id') }}"
        :is-app="{{ ($isApp) ? 'true' : 'false' }}"
        :room="{{ $room->toJson() }}"
        :user="{{ ($user) ? $user->toJson() : "{}"}}">
    </room-message-viewer-component>
@endsection


@push('scripts')
@endpush
