<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MessageImage extends Model
{
    protected $guarded = ['id'];

    protected $appends = ['image_path'];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getImagePathAttribute(): string
    {
        return '/storage/message_images/' . $this->user_id . '/' . $this->filename;
    }

    protected static function booted()
    {
        // Eloquent 経由削除時にストレージ実体も削除する
        // DB DELETE 成功後にファイル削除する順序で broken state（ファイル消失 + DB 残）を回避
        static::deleted(function (self $img) {
            Storage::disk('public')->delete("message_images/{$img->user_id}/{$img->filename}");
        });
    }
}
