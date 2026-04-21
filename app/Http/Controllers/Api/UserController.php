<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function getNewUsers(): JsonResponse
    {
        $users = User::orderBy('created_at', 'desc')->limit(10)->get();

        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->id,
                'name' => $user->name,
                'image_url' => url($user->getImagePath()),
            ];
        }

        return response()->json([
            'users' => $data,
        ]);
    }
}
