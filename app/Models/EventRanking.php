<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRanking extends Model
{
    protected $table = 'event_rankings';

    protected $guarded = ['id'];

    /**
     * このルームランキングを所有するユーザーを取得
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
