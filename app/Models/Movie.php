<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
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

    /**
     * この動画を所有するゲームを取得
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * 動画のいいねを取得
     */
    public function movie_goods()
    {
        return $this->hasMany(MovieGood::class);
    }

    // 動画サムネイルのパスを取得
    public function getImagePath() {
        $path = '/images/noimage-video.png';
        if ($this->image) {
            $path = '/storage/movies/'.$this->image;
        }
        return $path;
    }
}
