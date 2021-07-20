<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Socialite;

class FacebookController extends Controller
{

    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleProviderCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();

            if ($facebookUser) {
                // OAuth Two Providers
                $token = $facebookUser->token;
                $refreshToken = $facebookUser->refreshToken; // not always provided
                $expiresIn = $facebookUser->expiresIn;

                // All Providers
                //$facebookUser->getId();
                //$facebookUser->getNickname();
                //$facebookUser->getName();
                //$facebookUser->getEmail();
                //$facebookUser->getAvatar();
            }
        } catch(Exception $e) {
            return redirect('/');
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
        Auth::login($user, true);
        return redirect('/?api_token=' . $user->api_token);
    }
}
