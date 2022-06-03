<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieGood extends Model
{

    // 指定したカラムは、create()、fill()、update()で値が代入されない
    protected $guarded = ['id'];

    /**
     * このいいねを所有するユーザーを取得
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * このいいねを所有する動画を取得
     */
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
