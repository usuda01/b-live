<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    // 配信者の方へ
    public function liver()
    {
        return view('page.liver');
    }

    // リスナーの方へ
    public function listener()
    {
        return view('page.listener');
    }

    // 運営会社
    public function company()
    {
        return view('page.company');
    }

    // 配信者特典
    public function benefits()
    {
        return view('page.benefits');
    }

    // イラストレーター募集
    public function lp01()
    {
        return view('page.lp01');
    }

    // お知らせ
    public function notice()
    {
        return view('page.notice');
    }

    // お知らせ
    public function noticeDetail($pageId)
    {
        return view('page.notice.' . $pageId);
    }

    // 配信方法
    public function howto()
    {
        return view('page.howto');
    }

    // プライバシーポリシー
    public function privacy()
    {
        return view('page.privacy');
    }

    // レベルについて
    public function level()
    {
        return view('page.level');
    }

    // ランキングについて
    public function ranking()
    {
        return view('page.ranking');
    }

    // 利用規約
    public function terms()
    {
        return view('page.terms');
    }

    // コラム
    public function column()
    {
        return view('page.column');
    }

}
