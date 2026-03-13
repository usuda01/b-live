<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function appleSignin(Request $request): JsonResponse
    {
        $request->validate([
            'identity_token' => 'required|string',
        ]);

        // Apple の公開鍵を取得して identityToken を検証
        try {
            $payload = $this->verifyAppleToken($request->identity_token);
        } catch (\Exception $e) {
            Log::error('Apple Sign In failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Apple認証に失敗しました。',
            ], 401);
        }

        // apple_id (sub claim) でユーザーを検索
        $appleId = $payload->sub;
        $user = User::where('apple_id', $appleId)->first();

        if (!$user) {
            $email = $request->input('email');

            if (!$email) {
                return response()->json([
                    'message' => 'アカウントが見つかりません。先にWebサイトで登録してください。',
                ], 404);
            }

            $user = User::create([
                'name' => $request->input('name') ?: mb_strimwidth($email, 0, 24, '', 'UTF-8'),
                'email' => $email,
                'password' => '1',
                'api_token' => Str::random(80),
                'status' => 2,
                'apple_id' => $appleId,
            ]);
            $user->user_data()->create([
                'stripe_id' => '',
                'stripe_status' => 0,
                'point' => 0,
            ]);
        }

        // api_token が未設定の場合は生成
        if (!$user->api_token) {
            $user->api_token = Str::random(80);
            $user->save();
        }

        return response()->json([
            'user' => $user,
            'token' => $user->api_token,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
            'token' => $request->user()->api_token,
        ]);
    }

    private function verifyAppleToken(string $identityToken): object
    {
        // Apple の JWKS を取得
        $response = Http::timeout(10)->get('https://appleid.apple.com/auth/keys');
        if (!$response->successful()) {
            throw new \Exception('Failed to fetch Apple JWKS: HTTP ' . $response->status());
        }
        $keys = JWK::parseKeySet($response->json());

        // JWT をデコード・検証（署名、有効期限を自動検証）
        $payload = JWT::decode($identityToken, $keys);

        // issuer の検証
        if ($payload->iss !== 'https://appleid.apple.com') {
            throw new \Exception('Invalid issuer');
        }

        // audience の検証（iOSアプリの Bundle ID）
        $bundleId = config('services.apple_app.bundle_id');
        if ($payload->aud !== $bundleId) {
            throw new \Exception('Invalid audience');
        }

        return $payload;
    }
}
