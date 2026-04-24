<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wowza extends Model
{

    // 指定したカラムは、create()、fill()、update()で値が代入されない
    protected $guarded = ['id'];

    /**
     * Wowzaの所有するRoomを取得
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    /**
     * このWowzaを所有するユーザーを取得
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 配信投稿用サーバー URL を組み立てる
     * 例: SRS   → rtmps://stream.carol-i.com:1936/live
     *     Wowza → rtmps://5f1ee0e19125e.streamlock.net/blive
     */
    public static function buildServerUrl(): string
    {
        $host = config('services.wowza.ssl_host_name');
        $protocol = config('services.wowza.protocol');
        $port = config('services.wowza.port');
        $app = config('services.wowza.app');

        $portPart = $port ? ":{$port}" : '';

        return "{$protocol}://{$host}{$portPart}/{$app}";
    }

    /**
     * マスタープレイリスト URL を組み立てる
     * 例: SRS   → https://stream.carol-i.com/live/{key}_all.m3u8
     *     Wowza → https://5f1ee0e19125e.streamlock.net/blive/ngrp:{key}_all/playlist.m3u8
     */
    public static function buildHlsUrl(string $streamKey): string
    {
        $host = config('services.wowza.ssl_host_name');
        $app = config('services.wowza.app');

        return $app === 'live'
            ? "https://{$host}/{$app}/{$streamKey}_all.m3u8"
            : "https://{$host}/{$app}/ngrp:{$streamKey}_all/playlist.m3u8";
    }
}
