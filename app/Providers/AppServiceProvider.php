<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use App\Models\Notification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // ログイン中のユーザーに対する通知情報を全ビューで共有
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $globalNotifications = Notification::where('receiver_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                // 空のコレクションを設定
                $globalNotifications = collect(); 
            }

            $view->with('globalNotifications', $globalNotifications);
        });
    }
}
