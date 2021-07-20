<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $guarded = ['id'];

    /**
     * ブロックをしているするユーザーを取得
     */
    public function blockUser()
    {
        return $this->belongsTo(User::class, 'blocker_id');
    }

    /**
     * ブロックをされているユーザーを取得
     */
    public function blockedUser()
    {
        return $this->belongsTo(User::class, 'blocked_id');
    }


}
