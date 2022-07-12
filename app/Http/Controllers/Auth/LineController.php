<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Socialite;

class LineController extends Controller
{

    // ログイン
    public function redirectToProvider() {
        return Socialite::driver('line')->redirect();
    }

    // コールバック
    public function handleProviderCallback(Request $request) {

        $error = $request->input('error');
        if ($error) {
            return redirect('login');
        }

        try {
            $lineUser = Socialite::driver('line')->stateless()->user();
        } catch (Exception $e) {
            return redirect('auth/line');
        }

        if (!$lineUser->email) {
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
        Auth::login($user, true);
        $loginRedirect = $request->session()->get('loginRedirect', '/');
        $loginRedirect .= '?api_token=' . $user->api_token;
        return redirect($loginRedirect);
    }
}

