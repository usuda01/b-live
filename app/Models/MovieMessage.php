<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieMessage extends Model
{
    protected $guarded = ['id'];

    /**
     * このコメントを所有するユーザーを取得
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * このコメントを所有する動画を取得
     */
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
