<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{

    // 指定したカラムは、create()、fill()、update()で値が代入されない
    protected $guarded = ['id'];

    /**
     * このログを所有するルームを取得
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
