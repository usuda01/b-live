<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{

    // 指定したカラムは、create()、fill()、update()で値が代入されない
    protected $guarded = ['id'];

    /**
     * このクランを所有するユーザーを取得
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    // クラン画像のパスを取得
    public function getImagePath() {
        $path = '/images/noimage-group.png';
        if ($this->image) {
            $path = '/storage/group/' . $this->image;
        }
        return $path;
    }
}
