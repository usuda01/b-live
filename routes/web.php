<?php

use App\Http\Controllers\HomeController;
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
Route::get('room/{room_id}', 'RoomController@stream');
Route::post('room/count-views', 'RoomController@countViews');

// ログイン画面
Route::get('/login', 'LoginController@login')->name('login');

// Appleログイン
Route::get('auth/apple-signin', 'Auth\AppleSigninController@login');
// Appleコールバック
Route::post('auth/apple-signin/callback', 'Auth\AppleSigninController@callback');

// Facebookログイン
Route::get('auth/facebook', 'Auth\FacebookController@redirectToProvider');
// Facebookコールバック
Route::get('auth/facebook/callback', 'Auth\FacebookController@handleProviderCallback');

// Twitterログイン
Route::get('auth/twitter', 'Auth\TwitterController@redirectToProvider');
// TwitterコールバックURL
Route::get('auth/twitter/callback', 'Auth\TwitterController@handleProviderCallback');

// ログアウトURL
Route::get('auth/logout', 'Auth\LoginController@logout');

// クラン個別
Route::get('group/detail/{group_id}', 'GroupController@detail');

// ルームランキング
Route::get('room-ranking/{target_month}/{target_rank}', 'RoomRankingController@index');
Route::get('api/room-ranking/{target_month}/{target_rank}', 'RoomRankingController@getRooms');

// 検索
Route::get('search', 'RoomController@search');

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
        Route::get('/payment', 'SettingController@payment');
        Route::post('/payment-confirm', 'SettingController@paymentConfirm');
        Route::post('/payment-exec', 'SettingController@paymentExec');
        Route::get('/payment-complete', 'SettingController@paymentComplete');

        Route::get('/coin', 'SettingController@coin');
        Route::get('/coin-request', 'SettingController@coinRequest');
        Route::post('/coin-request', 'SettingController@coinRequestPost');

        // LINE連携
        Route::get('/line', 'SettingController@line')->name('line');

        Route::get('/profile', 'SettingController@profile');
        Route::post('/profile', 'SettingController@profilePost');
        Route::get('/stream/{room_id?}', 'SettingController@stream');
        Route::post('/stream', 'SettingController@streamPost');
        Route::get('/archive', 'SettingController@archive');

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
Route::get('page/benefits', 'PageController@benefits');
Route::get('page/company', 'PageController@company');
Route::get('page/liver', 'PageController@liver');
Route::get('page/notice', 'PageController@notice');
Route::get('page/notice/{id}', 'PageController@noticeDetail');
Route::get('page/howto', 'PageController@howto');
Route::get('page/ranking', 'PageController@ranking');
Route::get('page/terms', 'PageController@terms');
Route::get('page/privacy', 'PageController@privacy');
Route::get('page/lp01', 'PageController@lp01');

// for Xcode
Route::get('room-message/{room_id}', 'RoomController@message');
Route::get('room-info/{room_id}', 'RoomController@getInfo');

// for 動画用コメントビューワー
Route::get('room-message-viewer/{room_id}', 'RoomController@messageViewer')->name('room_message_viewer');

