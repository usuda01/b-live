<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Socialite;

class LineController extends Controller
{
    private const CODE_TTL_SECONDS = 60;

    // ログイン
    public function redirectToProvider(Request $request) {
        // iOS からのリクエストの場合、state をセッションに保存
        if ($request->query('platform') === 'ios') {
            $request->session()->put('line_auth_platform', 'ios');
            $request->session()->put('line_auth_state', $request->query('state'));
        }

        return Socialite::driver('line')->redirect();
    }

    // コールバック
    public function handleProviderCallback(Request $request) {

        $error = $request->input('error');
        if ($error) {
            // iOS の場合はエラー付きでアプリにリダイレクト
            if ($request->session()->pull('line_auth_platform') === 'ios') {
                $request->session()->forget('line_auth_state');
                return redirect('blive://auth/line?error=denied');
            }
            return redirect('login');
        }

        try {
            $lineUser = Socialite::driver('line')->stateless()->user();
        } catch (\Exception $e) {
            // iOS の場合はエラー付きでアプリにリダイレクト
            if ($request->session()->pull('line_auth_platform') === 'ios') {
                $request->session()->forget('line_auth_state');
                return redirect('blive://auth/line?error=auth_failed');
            }
            return redirect('auth/line');
        }

        if (!$lineUser->email) {
            // iOS の場合はエラー付きでアプリにリダイレクト
            if ($request->session()->pull('line_auth_platform') === 'ios') {
                $request->session()->forget('line_auth_state');
                return redirect('blive://auth/line?error=email_required');
            }
            return redirect('login');
        }

        // LINEログインで連携しているか判定
        $user = User::where('line_id', $lineUser->id)
            ->whereNull('facebook_id')
            ->whereNull('twitter_id')
            ->whereNull('apple_id')
            ->first();
        if (!$user) {
            $user = User::create([
                'name' => $lineUser->name,
                'email' => $lineUser->email,
                'password' => '1',
                'api_token' => Str::random(80),
                'status' => 2,
                'line_id' => $lineUser->id,
            ]);
            $user->user_data()->create([
                'stripe_id' => '',
                'stripe_status' => 0,
                'point' => 0,
            ]);
        }

        // iOS からのリクエストの場合、一時コードを発行してアプリにリダイレクト
        $platform = $request->session()->pull('line_auth_platform');
        $state = $request->session()->pull('line_auth_state');

        if ($platform === 'ios' && $state) {
            $code = Str::random(40);

            Cache::put("line_oauth_code:{$code}", [
                'user_id' => $user->id,
                'state' => $state,
            ], self::CODE_TTL_SECONDS);

            return redirect("blive://auth/line?code={$code}&state={$state}");
        }

        // Web（既存処理）
        Auth::login($user, true);
        $loginRedirect = $request->session()->get('loginRedirect', '/');
        $loginRedirect .= '?api_token=' . $user->api_token;
        return redirect($loginRedirect);
    }
}
