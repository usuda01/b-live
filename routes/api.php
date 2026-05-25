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
use App\Http\Controllers\Api\AppVersionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\RoomController as ApiRoomController;
use App\Http\Controllers\Api\StreamController;
use App\Http\Controllers\Api\StreamScheduleController;
use App\Http\Controllers\Api\UserController as ApiUserController;
use App\Http\Controllers\Api\FacebookAuthController;
use App\Http\Controllers\Api\LineAuthController;
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

// LINE Sign In (iOS)
Route::post('auth/line/start', [LineAuthController::class, 'start']);
Route::post('auth/line/complete', [LineAuthController::class, 'complete']);

// Facebook Sign In (iOS)
Route::post('auth/facebook/start', [FacebookAuthController::class, 'start']);
Route::post('auth/facebook/complete', [FacebookAuthController::class, 'complete']);

Route::group(['middleware' => ['api']], function () {
    Route::get('app-version', [AppVersionController::class, 'show']);
    Route::get('followers/{follow_id}', [FollowerController::class, 'getFollowers'])->where('follow_id', '[0-9]+');
    Route::get('get-games', [GameController::class, 'getGames']);
    Route::get('message', [MessageController::class, 'show']);
    Route::post('message', [MessageController::class, 'store']);
    Route::get('rooms', [ApiRoomController::class, 'index']);
    Route::get('rooms/{id}', [ApiRoomController::class, 'show'])->where('id', '[0-9]+');
    Route::post('rooms/count-views', [ApiRoomController::class, 'countViews']);
    Route::get('users/new', [ApiUserController::class, 'getNewUsers']);
    Route::get('users/follower-ranking', [ApiUserController::class, 'getFollowerRanking']);
    Route::get('users/{id}', [ApiUserController::class, 'show'])->where('id', '[0-9]+');
    Route::get('movie/get-goods/{movie_id}', [MovieController::class, 'getMovieGoods']);
    Route::post('movie/play/', [MovieController::class, 'play']);
    Route::get('movie-message', [MovieMessageController::class, 'show']);
    Route::post('movie-message', [MovieMessageController::class, 'store']);
    Route::post('movie-message-delete', [MovieMessageController::class, 'delete']);
    Route::get('room-supporters', [UserController::class, 'getRoomSupporters']);

    // 配信予定（公開）
    Route::get('schedules', [StreamScheduleController::class, 'timetable']);
    Route::get('schedules/{id}', [StreamScheduleController::class, 'show'])->where('id', '[0-9]+');

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
        Route::get('user/notification-settings', [UserController::class, 'getNotificationSettings']);
        Route::put('user/notification-settings', [UserController::class, 'updateNotificationSettings']);
        Route::post('user/profile', [ApiUserController::class, 'updateProfile']);

        // 配信設定
        Route::get('stream/config', [StreamController::class, 'config']);
        Route::post('stream/start', [StreamController::class, 'start']);
        Route::post('stream/update', [StreamController::class, 'update']);
        Route::post('stream/end', [StreamController::class, 'end']);

        // 配信予定（リマインド）
        Route::post('schedules/{id}/reminder', [StreamScheduleController::class, 'reminderStore'])->where('id', '[0-9]+');
        Route::delete('schedules/{id}/reminder', [StreamScheduleController::class, 'reminderDestroy'])->where('id', '[0-9]+');

        // 配信予定（配信者向けCRUD）
        Route::get('stream/schedules', [StreamScheduleController::class, 'index']);
        Route::post('stream/schedules', [StreamScheduleController::class, 'store']);
        Route::put('stream/schedules/{id}', [StreamScheduleController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('stream/schedules/{id}', [StreamScheduleController::class, 'destroy'])->where('id', '[0-9]+');
        Route::post('stream/schedules/{id}/duplicate', [StreamScheduleController::class, 'duplicate'])->where('id', '[0-9]+');

        Route::middleware('throttle:media')->withoutMiddleware('throttle:api')->group(function () {
            // 端末からのメディアアップロード
            Route::post('media/upload', [MediaController::class, 'upload']);

            // メディア管理（写真管理画面で利用、管理者のみ）
            Route::get('media', [MediaController::class, 'index']);
            Route::get('media/{userId}/{filename}/thumb', [MediaController::class, 'showThumbnail'])
                ->where(['userId' => '[0-9]+', 'filename' => '[A-Za-z0-9._-]+']);
            Route::get('media/{userId}/{filename}', [MediaController::class, 'show'])
                ->where(['userId' => '[0-9]+', 'filename' => '[A-Za-z0-9._-]+']);
            Route::delete('media/{userId}/{filename}', [MediaController::class, 'destroy'])
                ->where(['userId' => '[0-9]+', 'filename' => '[A-Za-z0-9._-]+']);
        });
    });
});

