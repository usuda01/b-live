<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\User;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ncmb\NCMB;
use Ncmb\Push;

class FollowerController extends Controller
{
    /*
     * フォローされている人一覧
     */
    public function followers() {
        $followerId = Auth::id();
        $followers = Follower::where('follower_id', $followerId)->get();
        foreach ($followers as $follower) {
            $follower->user_image_path = $follower->followUser->getImagePath();
            $follower->is_follow = true;
        }
        return view('follower.followers', [
            'followers' => $followers
        ]);
    }

    /*
     * フォローしている人一覧
     */
    public function follows() {
        $followerId = Auth::id();
        $followers = Follower::where('follower_id', $followerId)->get();
        foreach ($followers as $follower) {
            $follower->user_image_path = $follower->followUser->getImagePath();
            $follower->is_follow = true;
        }
        return view('follower.follows', [
            'followers' => $followers
        ]);
    }

    /*
     * 自分がフォローしている人を取得
     */
    public function getMyFollows(Request $request) {
        $followerId = Auth::id();
        $followers = Follower::where('follower_id', $followerId);
        $followers = $followers->with(['followUser' => function ($q) {
            $q->select('id', 'name');
        }]);

        $followers = $followers
            ->orderBy('followers.id', 'desc')
            ->paginate(10);
        foreach ($followers as $follower) {
            $followUser = User::where('id', $follower->follow_id)->first();
            $follower->user_image_path = $followUser->getImagePath();
            $follower->is_follow = true;
        }
        return $followers;
    }

    /*
     * 自分をフォローしている人を取得
     */
    public function getMyFollowers(Request $request) {
        $followerId = Auth::id();
        $followers = Follower::where('follow_id', $followerId);
        $followers = $followers->with(['followerUser' => function ($q) {
            $q->select('id', 'name');
        }]);

        $followers = $followers
            ->orderBy('followers.id', 'desc')
            ->paginate(10);

        // 自分がフォローしている人
        $follows = Follower::select('follow_id')->where('follower_id', $followerId)->get();
        $followIds = [];
        foreach ($follows as $follow) {
            $followIds[] = $follow->follow_id;
        }

        foreach ($followers as $follower) {
            $followUser = User::where('id', $follower->follower_id)->first();
            $follower->user_image_path = $followUser->getImagePath();
            // 自分がフォローしているかどうかの判定
            $follower->is_follow = false;
            if (in_array($follower->follower_id, $followIds)) {
                $follower->is_follow = true;
            }
        }
        return $followers;
    }

    /*
     * フォロワーの一覧取得
     */
    public function getFollowers($followId)
    {
        $followers = Follower::where('follow_id', $followId)->get();
        return $followers;
    }

    /*
     * フォロー処理
     */
    public function follow(Request $request)
    {
        $followId = $request->input('follow_id');
        $followerId = Auth::id();
        $follower = Follower::create([
            'follow_id' => $followId,
            'follower_id' => $followerId,
        ]);

        /*
         * フォローされたユーザーに
         * Push通知
         */
        if ($follower->followUser->device_token) {
            // ncmbmania/php-ncmbがguzzlehttp/guzzleの7系に対応していないためインストールできない
/*
            NCMB::initialize(config('services.ncmb.applicationkey'), config('services.ncmb.clientkey'));
            Push::Send(array(
                'immediateDeliveryFlag' => true,
                'target' => ['ios'],
                'title' => $follower->followerUser->name . 'さんにフォローされました',
                'badgeIncrementFlag' => false,
                'sound' => 'default',
                'searchCondition' => ['deviceToken' => $follower->followUser->device_token]
            ));
*/
        }

        // LINE通知
        if ($follower->followUser->user_data->is_line_connected == 1) {
            // 通知設定
            if ($follower->followUser->user_data->line_notice == 1) {
                $lineMessage = "{$follower->followUser->name}さん\n"
                    . "【{$follower->followerUser->name}】さんにフォローされました\n"
                    . config('app.url').'/user/'.$follower->followerUser->id;
                Helper::pushLineMessage($follower->followUser->line_id, $lineMessage);
            }
        }

        return $follower;
    }

    /*
     * フォロー解除処理
     */
    public function followCancel(Request $request)
    {
        $followId = $request->input('follow_id');
        $followerId = Auth::id();
        $followers = Follower::where([
            'follow_id' => $followId,
            'follower_id' => $followerId,
        ])->delete();

        return $followers;
    }
}
