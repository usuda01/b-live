<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wowza;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StreamController extends Controller
{
    public function config(Request $request): JsonResponse
    {
        $user = $request->user();

        $streamKey = Str::random(8);

        $wowza = Wowza::firstOrCreate(
            ['user_id' => $user->id],
            [
                'server_url' => 'rtmps://' . config('services.wowza.ssl_host_name') . '/blive',
                'stream_key' => $streamKey,
                'hls_url' => 'https://' . config('services.wowza.ssl_host_name') . '/blive/ngrp:' . $streamKey . '_all/playlist.m3u8',
                'started_at' => date('Y-m-d H:i:s'),
                'status' => 1,
            ]
        );

        return response()->json([
            'server_url' => $wowza->server_url,
            'stream_key' => $wowza->stream_key,
            'hls_url' => $wowza->hls_url,
        ]);
    }
}
