<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieViewLog extends Model
{

    // 指定したカラムは、create()、fill()、update()で値が代入されない
    protected $guarded = ['id'];

    /**
     * このログを所有する動画を取得
     */
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
