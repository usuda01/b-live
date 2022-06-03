<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{

    // 指定したカラムは、create()、fill()、update()で値が代入されない
    protected $guarded = ['id'];

    // ゲーム画像のパスを取得
    public function getImagePath() {
        $path = '/images/games/'.$this->id.'.jpg';
        return $path;
    }
}
