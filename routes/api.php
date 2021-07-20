<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['api']], function () {
    Route::get('followers/{follow_id}', 'FollowerController@getFollowers')->where('follow_id', '[0-9]+');
    Route::get('message', 'MessageController@show');
    Route::post('message', 'MessageController@store');
    Route::get('room-supporters', 'UserController@getRoomSupporters');

    // 認証が必要なページ
    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('block/flag', 'BlockController@flag');
        Route::post('block/flag-user', 'BlockController@flagUser');
        Route::get('block/get-block-users', 'BlockController@getBlockUsers');
        Route::post('block/block', 'BlockController@block');
        Route::post('block/un-block', 'BlockController@unBlock');
        Route::post('followers/follow', 'FollowerController@follow');
        Route::post('followers/follow-cancel', 'FollowerController@followCancel');
        Route::post('payment', 'PaymentController@store');

        // チャージ
        Route::post('charge', 'PaymentController@charge');

        // for Xcode
        Route::post('user/register-device-token', 'UserController@registerDeviceToken');
    });
});

