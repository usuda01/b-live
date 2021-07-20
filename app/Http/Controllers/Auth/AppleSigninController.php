<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use GeneaLabs\LaravelSocialiter\Facades\Socialiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AppleSigninController extends Controller
{
    public function __construct()
    {
//        $this->middleware('guest')->except('logout');
    }

    public function login()
    {
        return Socialite::driver("sign-in-with-apple")
            //->scopes(["name", "email"])
            ->scopes(["email"])
            ->redirect();
    }

    public function callback(Request $request)
    {
        // or use Socialiter to automatically manage user resolution and persistance
        // なぜか動かない
//        $user = Socialiter::driver("sign-in-with-apple")
//            ->login();

        // get abstract user object, not persisted
        try {
            $appleUser = Socialite::driver('sign-in-with-apple')->user();
        } catch (Exception $e) {
            return redirect('auth/apple-signin');
        }

        $user = User::where('apple_id', $appleUser->id)->first();
        if (!$user) {
            $user = User::create([
                'name' => mb_strimwidth($appleUser->email, 0, 24, '', 'UTF-8'),
                'email' => $appleUser->email,
                'password' => '1',
                'api_token' => Str::random(80),
                'status' => 2,
                'apple_id' => $appleUser->id,
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
