<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointRequest extends Model
{
    // 指定したカラムは、create()、fill()、update()で値が代入されない
    protected $guarded = ['id'];

    /**
     * この動画を所有するユーザーを取得
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
