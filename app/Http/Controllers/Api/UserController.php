<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Follower;
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

    public function show($userId): JsonResponse
    {
        $user = User::with('user_data')->find($userId);
        if (!$user) {
            return response()->json(['message' => 'ユーザーが見つかりません'], 404);
        }

        $isFollowing = null;
        $authUser = auth('api')->user();
        if ($authUser && $authUser->id !== $user->id) {
            $isFollowing = Follower::where('follow_id', $user->id)
                ->where('follower_id', $authUser->id)
                ->exists();
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'image_url' => url($user->getImagePath()),
                'profile' => $user->profile,
                'listener_level' => $user->user_data ? (int) $user->user_data->listener_level : 1,
                'follower_count' => $user->followers()->count(),
                'following_count' => $user->follows()->count(),
                'is_following' => $isFollowing,
            ],
        ]);
    }
}
