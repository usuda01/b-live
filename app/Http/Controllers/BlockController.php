<?php

namespace App\Http\Controllers;

//use App\Http\Controllers\Controller;
use App\Mail\ContentReported;
use App\Mail\UserReported;
use App\Models\Block;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlockController extends Controller
{
    /**
     * ブロックユーザーの一覧取得
     */
    public function getBlockUsers()
    {
        $blockerId = Auth::id();
        $blocks = Block::where('blocker_id', $blockerId)->get();
        $users = [];
        foreach ($blocks as $block) {
            $users[] = $block->blockedUser;
        }
        return $users;
    }

    /*
     * ブロック処理
     */
    public function block(Request $request) {
        $blockedId = $request->input('blocked_id');
        $blockerId = Auth::id();
        $block = Block::create([
            'blocked_id' => $blockedId,
            'blocker_id' => $blockerId,
        ]);

        return $block;
    }

    /*
     * ブロック解除処理
     */
    public function unBlock(Request $request)
    {
        $blockedId = $request->input('blocked_id');
        $blockerId = Auth::id();
        $blocks = Block::where([
            'blocked_id' => $blockedId,
            'blocker_id' => $blockerId,
        ])->delete();

        return $blocks;
    }

    /*
     * コンテンツ通報処理
     */
    public function flag(Request $request) {
        $roomId = $request->input('room_id');
        $room = Room::where('id', $roomId)->first();
        $user = Auth::user();
        $report = [
            'reporterId' => $user->id,
            'reporterName' => $user->name,
            'roomId' => $room->id,
            'roomName' => $room->name,
        ];
        \Mail::to('hiroshi0104@gmail.com')->send(new ContentReported($report));
        return true;
    }

    /*
     * ユーザー通報処理
     */
    public function flagUser(Request $request) {
        $userId = $request->input('user_id');
        $targetUser = User::where('id', $userId)->first();
        $user = Auth::user();
        $report = [
            'reporterId' => $user->id,
            'reporterName' => $user->name,
            'userId' => $targetUser->id,
            'userName' => $targetUser->name,
        ];
        \Mail::to('hiroshi0104@gmail.com')->send(new UserReported($report));
        return true;
    }
}

