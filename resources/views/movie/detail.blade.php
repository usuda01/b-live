@extends('layouts.app')

@if ($movie->game)
    @section('title', $movie->game->name . '｜' . $movie->name . ' - ショート動画' . ' - ')
@else
    @section('title', $movie->name . ' - ショート動画' . ' - ')
@endif

@section('content')
    <movie-component
        :admin-user-id="{{ Config::get('services.admin_user_id') }}"
        :movie="{{ $movie->toJson() }}"
        :user="{{ ($user) ? $user->toJson() : "{}" }}"
    >
    </movie-component>
@endsection

@push('meta')
    <meta property="og:image" content="{{ Request::getSchemeAndHttpHost() }}/{{ $movie->image_path }}" />
    <meta property="twitter:image" content="{{ Request::getSchemeAndHttpHost() }}/{{ $movie->image_path }}" />
@endpush

@push('header-script')
<script type="application/ld+json">
{
    "@context": "http://schema.org",
    "@type": "VideoObject",
    "name": "@if($movie->game){{ $movie->game->name }}｜@endif{{ $movie->name }} - ショート動画",
    "description": "{{ $movie->name }}",
    "duration": "{{ \App\Helpers\Helper::timeToIso($movie->duration) }}",
    "thumbnailUrl": "{{ Request::getSchemeAndHttpHost() }}{{ $movie->image_path }}",
    "uploadDate": "{{ $movie->updated_at->format('Y-m-d') }}",
    "contentUrl": "{{ Request::getSchemeAndHttpHost() }}{{ $movie->getFilePath() }}"
}
</script>
@endpush
