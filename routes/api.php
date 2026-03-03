<?php

use App\Http\Controllers\BlockController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\MovieMessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StreamController;
use App\Http\Controllers\Api\TwitterAuthController;
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

// Apple Sign In (iOS)
Route::post('auth/apple-signin', [AuthController::class, 'appleSignin']);

// Twitter Sign In (iOS)
Route::post('auth/twitter/start', [TwitterAuthController::class, 'start']);
Route::post('auth/twitter/complete', [TwitterAuthController::class, 'complete']);

Route::group(['middleware' => ['api']], function () {
    Route::get('followers/{follow_id}', [FollowerController::class, 'getFollowers'])->where('follow_id', '[0-9]+');
    Route::get('get-games', [GameController::class, 'getGames']);
    Route::get('message', [MessageController::class, 'show']);
    Route::post('message', [MessageController::class, 'store']);
    Route::get('movie/get-goods/{movie_id}', [MovieController::class, 'getMovieGoods']);
    Route::post('movie/play/', [MovieController::class, 'play']);
    Route::get('movie-message', [MovieMessageController::class, 'show']);
    Route::post('movie-message', [MovieMessageController::class, 'store']);
    Route::post('movie-message-delete', [MovieMessageController::class, 'delete']);
    Route::get('room-supporters', [UserController::class, 'getRoomSupporters']);

    // 認証が必要なページ
    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('auth/me', [AuthController::class, 'me']);

        Route::post('block/flag', [BlockController::class, 'flag']);
        Route::post('block/flag-user', [BlockController::class, 'flagUser']);
        Route::get('block/get-block-users', [BlockController::class, 'getBlockUsers']);
        Route::post('block/block', [BlockController::class, 'block']);
        Route::post('block/un-block', [BlockController::class, 'unBlock']);
        Route::post('followers/follow', [FollowerController::class, 'follow']);
        Route::post('followers/follow-cancel', [FollowerController::class, 'followCancel']);
        Route::post('movie/good', [MovieController::class, 'good']);
        Route::post('movie/good-cancel', [MovieController::class, 'goodCancel']);
        Route::post('notifications/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::post('room/store-view-time', [RoomController::class, 'storeViewTime']);
        Route::post('payment', [PaymentController::class, 'store']);

        // チャージ
        Route::post('charge', [PaymentController::class, 'charge']);

        // for Xcode
        Route::post('user/register-device-token', [UserController::class, 'registerDeviceToken']);

        // 配信設定
        Route::get('stream/config', [StreamController::class, 'config']);
        Route::post('stream/start', [StreamController::class, 'start']);
        Route::post('stream/end', [StreamController::class, 'end']);
    });
});

