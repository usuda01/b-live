@extends('layouts.app')
@section('title', $room->name . ' - ')
@section('content')
    <room-component
        :admin-user-id="{{ Config::get('services.admin_user_id') }}"
        :is-app="{{ ($isApp) ? 'true' : 'false' }}"
        :room="{{ $room->toJson() }}"
        :user="{{ ($user) ? $user->toJson() : "{}" }}"
    >
    </room-component>
@endsection

@push('meta')
    <meta name="description" content="B-LIVE（ビーライブ）は、ゲームのライブ配信プラットフォームです！お気に入りの配信者を見つけて、一緒に盛り上がろう！">
    <meta property="og:description" content="B-LIVE（ビーライブ）は、ゲームのライブ配信プラットフォームです！お気に入りの配信者を見つけて、一緒に盛り上がろう！" />
    <meta name="twitter:description" content="ゲーム配信プラットフォーム、「B-LIVE」！！" />
    <meta property="og:image" content="{{ Request::getSchemeAndHttpHost() }}/{{ $room->image_path }}" />
    <meta property="twitter:image" content="{{ Request::getSchemeAndHttpHost() }}/{{ $room->image_path }}" />
@endpush

@if ($room->status == 1)
@push('header-script')
    <script type="application/ld+json">
    {
        "@context": "http://schema.org",
        "@type": "VideoObject",
        "name": "@if($room->game_id){{ $room->game->name }}｜@endif{{ $room->name }}",
        "description": "{{ $room->name }}",
        "thumbnailUrl": "{{ Request::getSchemeAndHttpHost() }}{{ $room->image_path }}",
        "uploadDate": "{{ $room->updated_at->format('Y-m-d') }}",
        "contentUrl": "{{ $room->wowza->hls_url }}",
        "publication": [
            {
                "@type": "BroadcastEvent",
                "isLiveBroadcast": true,
                "startDate": "{{ $room->created_at->format('Y-m-d H:i:s') }}",
                "endDate": "{{ $room->created_at->addHours(4)->format('Y-m-d H:i:s') }}"
            }
        ]
    }
</script>
@endpush
@endif

@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ mix('js/room.js') }}"></script>
    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
@endpush
