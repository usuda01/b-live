<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
    protected $table = 'user_datas';

    protected $guarded = ['id'];

    /**
     * このユーザーデータを所有するユーザーを取得
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
