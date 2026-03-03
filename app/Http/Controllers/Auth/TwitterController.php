<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Socialite;

class TwitterController extends Controller
{
    private const CODE_TTL_SECONDS = 60;

    // ログイン
    public function redirectToProvider(Request $request) {
        // iOS からのリクエストの場合、state をセッションに保存
        if ($request->query('platform') === 'ios') {
            $request->session()->put('twitter_auth_platform', 'ios');
            $request->session()->put('twitter_auth_state', $request->query('state'));
        }

        return Socialite::driver('twitter')->redirect();
    }

    // コールバック
    public function handleProviderCallback(Request $request) {

        $denied = $request->input('denied');
        if ($denied) {
            // iOS の場合はエラー付きでアプリにリダイレクト
            if ($request->session()->pull('twitter_auth_platform') === 'ios') {
                $request->session()->forget('twitter_auth_state');
                return redirect('blive://auth/twitter?error=denied');
            }
            return redirect('login');
        }

        try {
            $twitterUser = Socialite::driver('twitter')->user();
        } catch (\Exception $e) {
            return redirect('auth/twitter');
        }

        $user = User::where('twitter_id', $twitterUser->id)->first();
        if (!$user) {
            $email = $twitterUser->getEmail();
            if (!$email) {
                $email = $twitterUser->nickname . '@pseudo.twitter.com';
            }
            $user = User::create([
                'name' => mb_substr($twitterUser->name, 0, 24),
                'email' => $email,
                'password' => '1',
                'api_token' => Str::random(80),
                'status' => 2,
                'twitter_id' => $twitterUser->id,
            ]);
            $user->user_data()->create([
                'stripe_id' => '',
                'stripe_status' => 0,
                'point' => 0,
            ]);
        }

        // iOS からのリクエストの場合、一時コードを発行してアプリにリダイレクト
        $platform = $request->session()->pull('twitter_auth_platform');
        $state = $request->session()->pull('twitter_auth_state');

        if ($platform === 'ios' && $state) {
            $code = Str::random(40);

            Cache::put("twitter_oauth_code:{$code}", [
                'user_id' => $user->id,
                'state' => $state,
            ], self::CODE_TTL_SECONDS);

            return redirect("blive://auth/twitter?code={$code}&state={$state}");
        }

        // Web（既存処理）
        Auth::login($user, true);
        $loginRedirect = $request->session()->get('loginRedirect', '/');
        $loginRedirect .= '?api_token=' . $user->api_token;
        return redirect($loginRedirect);
    }
}
