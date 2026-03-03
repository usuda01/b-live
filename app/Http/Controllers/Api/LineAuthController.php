<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class LineAuthController extends Controller
{
    private const CODE_TTL_SECONDS = 60;

    /**
     * iOS向け LINE OAuth 開始
     * state を発行し、auth_url を返却
     */
    public function start(Request $request): JsonResponse
    {
        $state = Str::random(40);

        $authUrl = url('/auth/line') . '?' . http_build_query([
            'platform' => 'ios',
            'state' => $state,
        ]);

        return response()->json([
            'auth_url' => $authUrl,
            'state' => $state,
        ]);
    }

    /**
     * iOS向け 一時コードを api_token + user に交換
     */
    public function complete(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'state' => 'required|string',
        ]);

        $code = $request->code;
        $state = $request->state;

        // 一時コードをキャッシュから取得（単回利用）
        $cacheKey = "line_oauth_code:{$code}";
        $codeData = Cache::pull($cacheKey);

        if (!$codeData) {
            return response()->json([
                'message' => '認証コードが無効または期限切れです。',
            ], 401);
        }

        // state の一致確認
        if ($codeData['state'] !== $state) {
            return response()->json([
                'message' => '認証状態が一致しません。',
            ], 401);
        }

        $user = User::find($codeData['user_id']);
        if (!$user) {
            return response()->json([
                'message' => 'ユーザーが見つかりません。',
            ], 404);
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
}
