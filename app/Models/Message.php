<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = ['id'];

    public function paymentMessage()
    {
        return $this->payment();
    }

    /**
     * このコメントを所有するユーザーを取得
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * このコメントを所有するルームを取得
     */
    public function room()
    {
        return $this->belongsTo('App\Room');
    }

    /**
     * コメントに関連する支払いを取得
     */
    public function payment()
    {
        return $this->hasOne('App\Payment');
    }
}
