<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    protected $guarded = ['id'];

    /**
     * フォローされたユーザーを取得
     */
    public function followUser()
    {
        return $this->belongsTo(User::class, 'follow_id', 'id');
    }

    /**
     * フォローしたユーザーを取得
     */
    public function followerUser()
    {
        return $this->belongsTo(User::class, 'follower_id', 'id');
    }

}
