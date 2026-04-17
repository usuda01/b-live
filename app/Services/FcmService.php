<?php

namespace App\Services;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FcmService
{
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';
    private const CACHE_KEY = 'fcm_access_token';
    // Googleのアクセストークンは3600秒で失効するため10分のバッファを取る
    private const TOKEN_TTL_SECONDS = 3000;

    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function send(string $deviceToken, string $title, ?string $body = null, array $data = []): bool
    {
        $projectId = config('services.firebase.project_id');
        if (empty($projectId)) {
            Log::error('[FCM] FIREBASE_PROJECT_ID is not configured');
            return false;
        }

        $accessToken = $this->getAccessToken();
        if (empty($accessToken)) {
            return false;
        }

        $message = [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $title,
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                        ],
                    ],
                ],
            ],
        ];

        if (!is_null($body)) {
            $message['message']['notification']['body'] = $body;
        }

        if (!empty($data)) {
            $message['message']['data'] = array_map('strval', $data);
        }

        try {
            $response = $this->client->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                    'Content-Type' => 'application/json; UTF-8',
                ],
                'json' => $message,
                'http_errors' => false,
                'timeout' => 10,
            ]);

            $status = $response->getStatusCode();
            $responseBody = (string) $response->getBody();

            if ($status >= 200 && $status < 300) {
                return true;
            }

            Log::warning("[FCM] send failed: status={$status} body={$responseBody}");
            return false;
        } catch (\Throwable $e) {
            Log::error('[FCM] send exception: ' . $e->getMessage());
            return false;
        }
    }

    private function getAccessToken(): ?string
    {
        $cached = Cache::get(self::CACHE_KEY);
        if (!empty($cached)) {
            return $cached;
        }
        $token = $this->fetchAccessToken();
        if (!empty($token)) {
            Cache::put(self::CACHE_KEY, $token, self::TOKEN_TTL_SECONDS);
        }
        return $token;
    }

    private function fetchAccessToken(): ?string
    {
        $credentialsPath = config('services.firebase.credentials');
        $raw = @file_get_contents($credentialsPath ?? '');
        if ($raw === false) {
            Log::error('[FCM] Service account credentials not readable: ' . $credentialsPath);
            return null;
        }

        $credentials = json_decode($raw, true);
        if (empty($credentials['client_email']) || empty($credentials['private_key'])) {
            Log::error('[FCM] Invalid service account credentials');
            return null;
        }

        $now = time();
        $payload = [
            'iss' => $credentials['client_email'],
            'scope' => self::SCOPE,
            'aud' => self::TOKEN_URL,
            'exp' => $now + 3600,
            'iat' => $now,
        ];

        $assertion = JWT::encode($payload, $credentials['private_key'], 'RS256');

        try {
            $response = $this->client->post(self::TOKEN_URL, [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $assertion,
                ],
                'http_errors' => false,
                'timeout' => 10,
            ]);

            $data = json_decode((string) $response->getBody(), true);
            if (!empty($data['access_token'])) {
                return $data['access_token'];
            }

            Log::error('[FCM] Failed to fetch access token: ' . json_encode($data));
            return null;
        } catch (\Throwable $e) {
            Log::error('[FCM] Access token fetch exception: ' . $e->getMessage());
            return null;
        }
    }
}
