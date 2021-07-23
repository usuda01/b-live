<?php

use App\Http\Controllers\Auth\TwitterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomRankingController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [HomeController::class, 'index']);

// 問い合わせ
Route::get('contact', 'ContactController@form');
Route::post('contact', 'ContactController@send');
Route::get('contact/complete', 'ContactController@complete');

// 動画配信個別
Route::get('room/{room_id}', [RoomController::class, 'stream']);
Route::post('room/count-views', [RoomController::class, 'countViews']);

// ログイン画面
Route::get('/login', [LoginController::class, 'login'])->name('login');

// Appleログイン
Route::get('auth/apple-signin', 'Auth\AppleSigninController@login');
// Appleコールバック
Route::post('auth/apple-signin/callback', 'Auth\AppleSigninController@callback');

// Facebookログイン
Route::get('auth/facebook', 'Auth\FacebookController@redirectToProvider');
// Facebookコールバック
Route::get('auth/facebook/callback', 'Auth\FacebookController@handleProviderCallback');

// Twitterログイン
Route::get('auth/twitter', [TwitterController::class, 'redirectToProvider']);
// TwitterコールバックURL
Route::get('auth/twitter/callback', [TwitterController::class, 'handleProviderCallback']);

// ログアウトURL
Route::get('auth/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout']);

// クラン個別
Route::get('group/detail/{group_id}', 'GroupController@detail');

// ルームランキング
Route::get('room-ranking/{target_month}/{target_rank}', [RoomRankingController::class, 'index']);
Route::get('api/room-ranking/{target_month}/{target_rank}', [RoomRankingController::class, 'getRooms']);

// 検索
Route::get('search', [RoomController::class, 'search']);

// ユーザーページ
Route::get('user/{user_id}', 'UserController@detail');
Route::get('api/user', 'UserController@getRooms');

// イベントページ
Route::get('event', 'EventRankingController@index');

// 認証
Route::middleware('auth')->group(function () {

    // 自分がフォローしているユーザー一覧
    Route::get('followers/follows', 'FollowerController@follows');
    Route::get('api/followers/follows', 'FollowerController@getMyFollows');

    // 自分をフォローしているユーザー一覧
    Route::get('followers/followers', 'FollowerController@followers');
    Route::get('api/followers/followers', 'FollowerController@getMyFollowers');

    // 設定
    Route::prefix('setting')->group(function () {
        // プレミアム会員ページ
        Route::get('/payment', [SettingController::class, 'payment']);
        Route::post('/payment-confirm', [SettingController::class, 'paymentConfirm']);
        Route::post('/payment-exec', [SettingController::class, 'paymentExec']);
        Route::get('/payment-complete', [SettingController::class, 'paymentComplete']);

        Route::get('/coin', 'SettingController@coin');
        Route::get('/coin-request', 'SettingController@coinRequest');
        Route::post('/coin-request', 'SettingController@coinRequestPost');

        // LINE連携
        Route::get('/line', [SettingController::class, 'line'])->name('line');

        Route::get('/profile', [SettingController::class, 'profile']);
        Route::post('/profile', [SettingController::class, 'profilePost']);
        Route::get('/stream/{room_id?}', [SettingController::class, 'stream']);
        Route::post('/stream', [SettingController::class, 'streamPost']);
        Route::get('/archive', [SettingController::class, 'archive']);

        // クラン
        Route::get('/group-list', 'SettingController@groupList');
        Route::get('/group/{group_id?}', 'SettingController@group');
        Route::get('/group', 'SettingController@group');
        Route::post('/group', 'SettingController@groupPost');
    });
});

// LINE連携
Route::prefix('setting')->group(function () {
    Route::post('line-callback', 'SettingController@lineCallback');
});

// 静的ページ
Route::get('page/benefits', [PageController::class, 'benefits']);
Route::get('page/company', [PageController::class, 'company']);
Route::get('page/liver', [PageController::class, 'liver']);
Route::get('page/notice', [PageController::class, 'notice']);
Route::get('page/notice/{id}', [PageController::class, 'noticeDetail']);
Route::get('page/howto', [PageController::class, 'howto']);
Route::get('page/ranking', [PageController::class, 'ranking']);
Route::get('page/terms', [PageController::class, 'terms']);
Route::get('page/privacy', [PageController::class, 'privacy']);
Route::get('page/lp01', [PageController::class, 'lp01']);

// for Xcode
Route::get('room-message/{room_id}', [RoomController::class, 'message']);
Route::get('room-info/{room_id}', [RoomController::class, 'getInfo']);

// for 動画用コメントビューワー
Route::get('room-message-viewer/{room_id}', [RoomController::class, 'messageViewer'])->name('room_message_viewer');

