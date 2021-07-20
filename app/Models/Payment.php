<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = ['id'];

    /**
     * この支払いを所有するユーザーを取得
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * この支払いを所有するメッセージを取得
     */
    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
