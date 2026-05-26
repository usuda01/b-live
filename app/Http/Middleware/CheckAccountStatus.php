<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAccountStatus
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user && $user->isBanned()) {
            return response()->json([
                'error_code' => 'ACCOUNT_BANNED',
                'message' => 'このアカウントは利用が停止されています。',
            ], 403);
        }

        return $next($request);
    }
}
