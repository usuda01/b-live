<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Socialite;

class FacebookController extends Controller
{
    private const CODE_TTL_SECONDS = 60;

    public function redirectToProvider(Request $request)
    {
        // iOS からのリクエストの場合、state をセッションに保存
        if ($request->query('platform') === 'ios') {
            $request->session()->put('facebook_auth_platform', 'ios');
            $request->session()->put('facebook_auth_state', $request->query('state'));
        }

        return Socialite::driver('facebook')->redirect();
    }

    public function handleProviderCallback(Request $request)
    {
        $error = $request->input('error');
        if ($error) {
            // iOS の場合はエラー付きでアプリにリダイレクト
            if ($request->session()->pull('facebook_auth_platform') === 'ios') {
                $request->session()->forget('facebook_auth_state');
                return redirect('blive://auth/facebook?error=denied');
            }
            return redirect('login');
        }

        try {
            $facebookUser = Socialite::driver('facebook')->user();
        } catch (\Exception $e) {
            // iOS の場合はエラー付きでアプリにリダイレクト
            if ($request->session()->pull('facebook_auth_platform') === 'ios') {
                $request->session()->forget('facebook_auth_state');
                return redirect('blive://auth/facebook?error=auth_failed');
            }
            return redirect('/');
        }

        if (!$facebookUser->getEmail()) {
            // iOS の場合はエラー付きでアプリにリダイレクト
            if ($request->session()->pull('facebook_auth_platform') === 'ios') {
                $request->session()->forget('facebook_auth_state');
                return redirect('blive://auth/facebook?error=email_required');
            }
            return redirect('login');
        }

        $user = User::where('facebook_id', $facebookUser->getId())->first();
        if (!$user) {
            $user = User::create([
                'name' => $facebookUser->getName(),
                'email' => $facebookUser->getEmail(),
                'password' => '1',
                'api_token' => Str::random(80),
                'status' => 2,
                'facebook_id' => $facebookUser->getId(),
            ]);
            $user->user_data()->create([
                'stripe_id' => '',
                'stripe_status' => 0,
                'point' => 0,
            ]);
        }

        // iOS からのリクエストの場合、一時コードを発行してアプリにリダイレクト
        $platform = $request->session()->pull('facebook_auth_platform');
        $state = $request->session()->pull('facebook_auth_state');

        if ($platform === 'ios' && $state) {
            $code = Str::random(40);

            Cache::put("facebook_oauth_code:{$code}", [
                'user_id' => $user->id,
                'state' => $state,
            ], self::CODE_TTL_SECONDS);

            return redirect("blive://auth/facebook?code={$code}&state={$state}");
        }

        // Web（既存処理）
        Auth::login($user, true);
        $loginRedirect = $request->session()->get('loginRedirect', '/');
        $loginRedirect .= '?api_token=' . $user->api_token;
        return redirect($loginRedirect);
    }
}
