<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

// アプリ（iOS等）に埋め込むWebコンテンツを返すコントローラ。
// 中身・出し分けはサーバー側で制御し、アプリの再申請なしで差し替え可能にする。
class AppController extends Controller
{
    // アプリ内に埋め込むバナー
    public function banner()
    {
        return view('app.banner');
    }
}
