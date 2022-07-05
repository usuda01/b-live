<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomRanking extends Model
{
    protected $table = 'room_rankings';

    protected $guarded = ['id'];

    /**
     * このルームランキングを所有するルームを取得
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * このルームランキングを所有するユーザーを取得
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
