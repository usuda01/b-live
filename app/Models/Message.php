<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = ['id'];

    public function paymentMessage()
    {
        return $this->payment();
    }

    /**
     * このコメントを所有するユーザーを取得
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * このコメントを所有するルームを取得
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * コメントに関連する支払いを取得
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * コメントに紐付く画像を取得
     */
    public function image()
    {
        return $this->hasOne(MessageImage::class);
    }

    protected static function booted()
    {
        // 親 Message を Eloquent 経由で削除した際、子の MessageImage の deleted フックを
        // 明示的に発火させてストレージファイルも削除する。
        // - DB レベルの ON DELETE CASCADE は残しておく（直接 SQL や mass delete 時の DB 整合性用）
        // - $msg->image()->delete() ではなく $msg->image->delete() を呼ぶこと
        //   （前者は query builder の mass delete 扱いでフック非発火）
        static::deleting(function (self $msg) {
            if ($msg->image) {
                $msg->image->delete();
            }
        });
    }
}
