<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Room extends Model
{

    // 指定したカラムは、create()、fill()、update()で値が代入されない
    protected $guarded = ['id', 'image'];

    protected $dates = [
        'published_at',
        'finished_at',
    ];

    /**
     * Roomの所有するMessageを取得
     */
    public function messages()
    {
        return $this->hasMany('App\Message');
    }

    /**
     * Roomの所有するRoomRankingを取得
     */
    public function roomRankings()
    {
        return $this->hasMany('App\RoomRanking');
    }

    /**
     * このルームを所有するユーザーを取得
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * このルームを所有するWowzaを取得
     */
    public function wowza()
    {
        return $this->belongsTo('App\Wowza');
    }

    // 配信中ルーム画像のパスを取得
    public function getStreamImagePath() {
        /*
         * TODO サムネイルを取得できるようにする
         * 注意：REST APIのlimit Connections 60 per minute from the same IP address
         */
/*
        $path = '/images/noimage-video.png';
        if ($this->status == '1') {
            $path = '/storage/' . $this->stream_key . '.m3u8-thumbnail.png';
        } else if ($this->status == '2') {
            if ($this->image) {
                $path = '/storage/' . $this->image;
            }
        }
        return $path;
*/
        $path = '/images/noimage-video.png';
        if ($this->image) {
            $path = '/storage/rooms/'.$this->image;
        }
        return $path;
    }

    // ルーム画像のパスを取得
    public function getImagePath() {
        $path = '/images/noimage-video.png';
        if ($this->image) {
            $path = '/storage/rooms/'.$this->image;
        }
        return $path;
    }

    /*
     * 動画のランクをつける
     */
    public function updateRank() {
        if ($this->max_view <= 9) {
            // Dランク(1)
        } else if ($this->max_view >= 10 && $this->max_view <= 49) {
            // Cランク(2)
            if ($this->rank < 2) {
                $this->rank = 2;
                $this->save();
            }
        } else if ($this->max_view >= 50 && $this->max_view <= 99) {
            // Bランク(3)
            if ($this->rank < 3) {
                $this->rank = 3;
                $this->save();
            }
        } else if ($this->max_view >= 100) {
            // Aランク(4)
            if ($this->rank < 4) {
                $this->rank = 4;
                $this->save();
            }
        }
    }

    /*
     * 公開中→終了にした時の処理
     * ここではsaveせず、Controller側でsaveする
     */
    public function finish() {
        $this->status = 2;
        $this->finished_at = date('Y-m-d H:i:s');
        $this->stream_time = Helper::timeDiff($this->published_at, $this->finished_at);
        $this->wowza->status = 2;

        // wowzaのlivestreamを止める
        $wowza = Wowza::where('id', $this->wowza_id)->first();
        $wowza->finished_at = date('Y-m-d H:i:s');
        $wowza->save();
//        $this->save();
    }
}
