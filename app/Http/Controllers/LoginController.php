<?php
namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    // ログイン
    public function login(Request $request)
    {
        return view('login', []);
    }

    // ログアウト
    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/');
    }
}
