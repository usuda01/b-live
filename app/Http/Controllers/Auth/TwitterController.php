<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Socialite;

class TwitterController extends Controller
{

    // ログイン
    public function redirectToProvider() {
        return Socialite::driver('twitter')->redirect();
    }

    // コールバック
    public function handleProviderCallback(Request $request) {

        $denied = $request->input('denied');
        if ($denied) {
            return redirect('login');
        }

        try {
            $twitterUser = Socialite::driver('twitter')->user();
        } catch (Exception $e) {
            return redirect('auth/twitter');
        }

        $user = User::where('twitter_id', $twitterUser->id)->first();
        if (!$user) {
            $user = User::create([
                'name' => mb_substr($twitterUser->name, 0, 24),
                'email' => $twitterUser->nickname . '@pseudo.twitter.com',
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
        Auth::login($user, true);
        return redirect('/?api_token=' . $user->api_token);
    }
}
