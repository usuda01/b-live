@extends('layouts.app')

@section('title', $movie->name . ' - ')

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

@push('script')
<script type="application/ld+json">
{
    "@context": "http://schema.org",
    "@type": "VideoObject",
    "name": "@if($movie->game){{ $movie->game->name }}｜@endif{{ $movie->name }}",
    "description": "{{ $movie->name }}",
    "thumbnailUrl": "{{ Request::getSchemeAndHttpHost() }}{{ $movie->image_path }}",
    "uploadDate": "{{ $movie->updated_at->format('Y-m-d') }}",
    "contentUrl": "{{ Request::getSchemeAndHttpHost() }}{{ $movie->getFilePath() }}",
}
</script>
@endpush
