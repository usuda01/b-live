<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{

    /**
     * iPhoneチャージ処理
     */
    public function store(Request $request)
    {
        $userId = Auth::id();
        $productId = $request->input('product_id');
        $roomId = $request->input('room_id');
        $room = Room::where('id', $roomId)->first();

        $amount = 0;
        if ($productId == 'com.carol_i.www.B_LIVE.product1') {
            $amount = 84;
        } else if ($productId == 'com.carol_i.www.B_LIVE.product4') {
            $amount = 343;
        } else if ($productId == 'com.carol_i.www.B_LIVE.product5') {
            $amount = 686;
        } else if ($productId == 'com.carol_i.www.B_LIVE.product6') {
            $amount = 1540;
        }

        // ポイント加算
        $user = User::where('id', $userId)->first();
        $user->user_data->point += $amount;
        $user->push();

        return [
            'result' => 'OK'
        ];
    }

    /**
     * チャージ処理
     */
    public function charge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token_id' => 'required',
        ]);
        if ($validator->fails()) {
            return false;
        }

        $userId = Auth::id();
        $token = $request->input('token_id');
        $amount = $request->input('charge_amount');

        // Stripe課金
        \Stripe\Stripe::setApiKey(config('services.stripe.secret_key'));
        $charge = \Stripe\Charge::create([
            'source' => $token,
            'amount' => $amount,
            'currency' => 'jpy',
            'description' => 'user_id:'.Auth::id(),
        ]);

        // ポイント加算
        $user = User::where('id', $userId)->first();
        if ($amount == 220) {
            $user->user_data->point += 200;
        } else if ($amount == 1100) {
            $user->user_data->point += 1000;
        } else if ($amount == 2200) {
            $user->user_data->point += 2000;
        }
        $user->push();

        return [
            'result' => 'OK'
        ];
    }
}
