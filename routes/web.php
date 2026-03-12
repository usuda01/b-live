<?php

use App\Http\Controllers\Auth\AppleSigninController;
use App\Http\Controllers\Auth\FacebookController;
use App\Http\Controllers\Auth\LineController;
use App\Http\Controllers\Auth\TwitterController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EventRankingController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomRankingController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\UserController;
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

// コラム
Route::group(['prefix' => 'column'], function () {
    Route::get('column.html', [ColumnController::class, 'column']);
    Route::get('column01.html', [ColumnController::class, 'column01']);
    Route::get('column02.html', [ColumnController::class, 'column02']);
    Route::get('column03.html', [ColumnController::class, 'column03']);
    Route::get('column04.html', [ColumnController::class, 'column04']);
    Route::get('column05.html', [ColumnController::class, 'column05']);
    Route::get('column06.html', [ColumnController::class, 'column06']);
    Route::get('column07.html', [ColumnController::class, 'column07']);
    Route::get('column08.html', [ColumnController::class, 'column08']);
    Route::get('column09.html', [ColumnController::class, 'column09']);
    Route::get('column10.html', [ColumnController::class, 'column10']);
});

// サイトマップ
Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::group(['prefix' => 'sitemap'], function () {
    Route::get('game.xml', [SitemapController::class, 'game']);
    Route::get('movie.xml', [SitemapController::class, 'movie']);
    Route::get('page.xml', [SitemapController::class, 'page']);
});

// 問い合わせ
Route::get('contact', [ContactController::class, 'form']);
Route::post('contact', [ContactController::class, 'send']);
Route::get('contact/complete', [ContactController::class, 'complete']);

// 動画配信個別
Route::get('room/{room_id}', [RoomController::class, 'stream']);
Route::post('room/count-views', [RoomController::class, 'countViews']);

// ログイン画面
Route::get('/login', [LoginController::class, 'login'])->name('login');

// LINEログイン
Route::get('auth/line', [LineController::class, 'redirectToProvider']);
// LINEコールバック
Route::get('auth/line/callback', [LineController::class, 'handleProviderCallback']);

// Appleログイン
Route::get('auth/apple-signin', [AppleSigninController::class, 'login']);
// Appleコールバック
Route::post('auth/apple-signin/callback', [AppleSigninController::class, 'callback']);

// Facebookログイン
Route::get('auth/facebook', [FacebookController::class, 'redirectToProvider']);
// Facebookコールバック
Route::get('auth/facebook/callback', [FacebookController::class, 'handleProviderCallback']);

// Twitterログイン
Route::get('auth/twitter', [TwitterController::class, 'redirectToProvider']);
// TwitterコールバックURL
Route::get('auth/twitter/callback', [TwitterController::class, 'handleProviderCallback']);

// ログアウトURL
Route::get('auth/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout']);

// クラン個別
Route::get('group/detail/{group_id}', [GroupController::class, 'detail']);

// 動画個別
Route::get('movie/search', [MovieController::class, 'search']);
Route::get('movie/detail/{movie_id}', [MovieController::class, 'detail']);

// ルームランキング
Route::get('room-ranking/{target_month}/{target_rank}', [RoomRankingController::class, 'index']);
Route::get('api/room-ranking/{target_month}/{target_rank}', [RoomRankingController::class, 'getRooms']);

// 検索
Route::get('search', [SearchController::class, 'index']);
Route::get('api/search-movie', [SearchController::class, 'searchMovies']);
Route::get('api/search-room', [SearchController::class, 'searchRooms']);
Route::get('api/search-user', [SearchController::class, 'searchUsers']);

// ユーザーページ
Route::get('user/{user_id}', [UserController::class, 'detail']);
Route::get('api/user', [UserController::class, 'getRooms']);

// イベントページ
Route::get('event', [EventRankingController::class, 'index']);
Route::get('event2', [EventRankingController::class, 'event2']);
Route::get('event3', [EventRankingController::class, 'event3']);
Route::get('event4', [EventRankingController::class, 'event4']);
Route::get('event5', [EventRankingController::class, 'event5']);
Route::get('event6', [EventRankingController::class, 'event6']);
Route::get('event7', [EventRankingController::class, 'event7']);
Route::get('event8', [EventRankingController::class, 'event8']);
Route::get('event9', [EventRankingController::class, 'event9']);
Route::get('event10', [EventRankingController::class, 'event10']);
Route::get('event11', [EventRankingController::class, 'event11']);
Route::get('event12', [EventRankingController::class, 'event12']);
Route::get('event13', [EventRankingController::class, 'event13']);
Route::get('event14', [EventRankingController::class, 'event14']);

// 認証
Route::middleware('auth')->group(function () {

    // 自分がフォローしているユーザー一覧
    Route::get('followers/follows', [FollowerController::class, 'follows']);
    Route::get('api/followers/follows', [FollowerController::class, 'getMyFollows']);

    // 自分をフォローしているユーザー一覧
    Route::get('followers/followers', [FollowerController::class, 'followers']);
    Route::get('api/followers/followers', [FollowerController::class, 'getMyFollowers']);

    // 設定
    Route::prefix('setting')->group(function () {
        // プレミアム会員ページ
        Route::get('/payment', [SettingController::class, 'payment']);
        Route::post('/payment-confirm', [SettingController::class, 'paymentConfirm']);
        Route::post('/payment-exec', [SettingController::class, 'paymentExec']);
        Route::get('/payment-complete', [SettingController::class, 'paymentComplete']);

        Route::get('/coin', [SettingController::class, 'coin']);
        Route::get('/coin-request', [SettingController::class, 'coinRequest']);
        Route::post('/coin-request', [SettingController::class, 'coinRequestPost']);

        // LINE連携
        Route::get('/line', [SettingController::class, 'line'])->name('line');

        Route::get('/profile', [SettingController::class, 'profile']);
        Route::post('/profile', [SettingController::class, 'profilePost']);

        // 通知設定
        Route::get('/notice', [SettingController::class, 'notice']);
        Route::post('/notice', [SettingController::class, 'noticePost']);

        Route::get('/stream/{room_id?}', [SettingController::class, 'stream']);
        Route::post('/stream', [SettingController::class, 'streamPost']);
        Route::get('/archive', [SettingController::class, 'archive']);

        // クラン
        Route::get('/group-list', [SettingController::class, 'groupList']);
        Route::get('/group/{group_id?}', [SettingController::class, 'group']);
        Route::get('/group', [SettingController::class, 'group']);
        Route::post('/group', [SettingController::class, 'groupPost']);

        // 動画
        Route::get('/movie-list', [SettingController::class, 'movieList']);
        Route::get('/movie/{movie_id?}', [SettingController::class, 'movie']);
        Route::post('/movie', [SettingController::class, 'moviePost']);
    });
});

// LINE連携
Route::prefix('setting')->group(function () {
    Route::post('line-callback', [SettingController::class, 'lineCallback']);
});

// 静的ページ
Route::get('page/benefits', [PageController::class, 'benefits']);
Route::get('page/company', [PageController::class, 'company']);
Route::get('page/liver', [PageController::class, 'liver']);
Route::get('page/listener', [PageController::class, 'listener']);
Route::get('page/notice', [PageController::class, 'notice']);
Route::get('page/notice/{id}', [PageController::class, 'noticeDetail'])->whereNumber('id');
Route::get('page/howto', [PageController::class, 'howto']);
Route::get('page/level', [PageController::class, 'level']);
Route::get('page/ranking', [PageController::class, 'ranking']);
Route::get('page/terms', [PageController::class, 'terms']);
Route::get('page/privacy', [PageController::class, 'privacy']);
Route::get('page/lp01', [PageController::class, 'lp01']);

// for Xcode
Route::get('room-message/{room_id}', [RoomController::class, 'message']);
Route::get('room-info/{room_id}', [RoomController::class, 'getInfo']);

// for 動画用コメントビューワー
Route::get('room-message-viewer/{room_id}', [RoomController::class, 'messageViewer'])->name('room_message_viewer');
