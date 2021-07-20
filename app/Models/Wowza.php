<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7;

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

    /*
     * Wowza Streaming Cloud を使用時
     */
/*
    public function create() {
        $method = 'POST';
        $uri = '/api/v1.5/live_streams';
        $apiKey = config('services.wowza.api_key');
        $accessKey = config('services.wowza.access_key');
        $time = time();
        $signature = hash_hmac('sha256', $time.':'.$uri.':'.$apiKey, $apiKey);
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.cloud.wowza.com',
        ]);
        $headers = [
//            'wsc-api-key' => $apiKey, // sandbox
            'wsc-access-key' => $accessKey,
            'wsc-timestamp' => $time,
            'wsc-signature' => $signature,
            'Content-Type' => 'application/json'
        ];
        $options = [
            'headers' => $headers,
            'json' => [
                'live_stream' => [
                    'aspect_ratio_height' => 1080,
                    'aspect_ratio_width' => 1920,
                    'billing_mode' => 'pay_as_you_go',
                    'broadcast_location' => 'asia_pacific_japan',
                    'encoder' => 'other_rtmp',
                    'name' => 'B-LIVE40',
                    'transcoder_type' => 'transcoded',
                    'disable_authentication' => true,
                    'low_latency' => true,
                    'target_delivery_protocol' => 'hls-https'
                ]
            ]
        ];
        try {
            $response = $client->request($method, $uri, $options);
        } catch (ClientException $e) {
            echo Psr7\str($e->getResponse());
        }
        $result = json_decode($response->getBody()->getContents(), true);


        $id = $result['live_stream']['id'];
        $primaryServer = $result['live_stream']['source_connection_information']['primary_server'];
        $streamName = $result['live_stream']['source_connection_information']['stream_name'];
        $playerHlsPlaybackUrl = $result['live_stream']['player_hls_playback_url'];

        $wowza = new Wowza();
        $wowza->wowza_id = $id;
        $wowza->user_id = null;
        $wowza->server_url = $primaryServer;
        $wowza->stream_key = $streamName;
        $wowza->hls_url = $playerHlsPlaybackUrl;
        $wowza->started_at = null;
        $wowza->finished_at = null;
        $wowza->status = 2;
        $wowza->save();

    }
*/

    public function reset() {

        $method = 'PUT';
        $uri = '/api/v1.5/live_streams/' . $this->wowza_id . '/reset';
        $apiKey = config('services.wowza.api_key');
        $accessKey = config('services.wowza.access_key');
        $time = time();
        // 仕様書には書いてない
//        $signature = hash_hmac('sha256', $time.':'.$uri.':'.$apiKey, $apiKey);
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.cloud.wowza.com',
        ]);
        $headers = [
            'wsc-api-key' => $apiKey,
            'wsc-access-key' => $accessKey,
            // 仕様書には書いてない
//            'wsc-timestamp' => $time,
            'Content-Type' => 'application/json'
        ];
        $options = [
            'headers' => $headers,
            'json' => []
        ];
        try {
            $response = $client->request($method, $uri, $options);
//            $this->started_at = date('Y-m-d H:i:s');
//            $this->save();
        } catch (ClientException $e) {
            // 既に開始していた場合はエラーとなる
            //echo Psr7\str($e->getResponse());
        }
        //$result = json_decode($response->getBody()->getContents(), true);
    }

    public function start() {

        $method = 'PUT';
        $uri = '/api/v1.5/live_streams/' . $this->wowza_id . '/start';
        $apiKey = config('services.wowza.api_key');
        $accessKey = config('services.wowza.access_key');
        $time = time();
        // 仕様書には書いてない
//        $signature = hash_hmac('sha256', $time.':'.$uri.':'.$apiKey, $apiKey);
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.cloud.wowza.com',
        ]);
        $headers = [
            'wsc-api-key' => $apiKey,
            'wsc-access-key' => $accessKey,
            // 仕様書には書いてない
//            'wsc-timestamp' => $time,
            'Content-Type' => 'application/json'
        ];
        $options = [
            'headers' => $headers,
            'json' => []
        ];
        try {
            $response = $client->request($method, $uri, $options);
            $this->started_at = date('Y-m-d H:i:s');
            $this->save();
        } catch (ClientException $e) {
            // 既に開始していた場合はエラーとなる
            //echo Psr7\str($e->getResponse());
        }
        //$result = json_decode($response->getBody()->getContents(), true);
    }

    public function stop() {

        $method = 'PUT';
        $uri = '/api/v1.5/live_streams/' . $this->wowza_id . '/stop';
        $apiKey = config('services.wowza.api_key');
        $accessKey = config('services.wowza.access_key');
        $time = time();
        // 仕様書には書いてない
//        $signature = hash_hmac('sha256', $time.':'.$uri.':'.$apiKey, $apiKey);
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.cloud.wowza.com',
        ]);
        $headers = [
            'wsc-api-key' => $apiKey,
            'wsc-access-key' => $accessKey,
            // 仕様書には書いてない
//            'wsc-timestamp' => $time,
            'Content-Type' => 'application/json'
        ];
        $options = [
            'headers' => $headers,
            'json' => []
        ];
        try {
            $response = $client->request($method, $uri, $options);
            $this->finished_at = date('Y-m-d H:i:s');
            $this->save();
        } catch (ClientException $e) {
            // 既に開始していた場合はエラーとなる
            //echo Psr7\str($e->getResponse());
        }
        //$result = json_decode($response->getBody()->getContents(), true);
    }


}
