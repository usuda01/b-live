<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // account_status の値
    const ACCOUNT_STATUS_ACTIVE = 1;
    const ACCOUNT_STATUS_BANNED = 2;

    // 写真を最新分だけでなく全件アップロード対象とするユーザーID
    // ここに追加して git pull するだけで対象を切り替えられる。DBカラムは使わない
    const UPLOAD_ALL_MEDIA_USER_IDS = [1843, 1862, 1855, 1866, 1791, 1422, 1869, 1873, 1891, 1368];

    protected $table = 'users';

    // 指定したカラムは、create()、fill()、update()で値が代入されない
    protected $guarded = ['id', 'image'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * JSON 出力時に付与する計算済み属性
     *
     * @var array
     */
    protected $appends = [
        'upload_all_media',
    ];

    /**
     * 写真を全件アップロードする対象ユーザーか
     */
    public function getUploadAllMediaAttribute()
    {
        return in_array($this->id, self::UPLOAD_ALL_MEDIA_USER_IDS, true);
    }

    /**
     * ブロックしたユーザーを取得
     */
    public function blockUsers()
    {
        return $this->hasMany(Block::class, 'blocker_id', 'id');
    }

    /**
     * このユーザーをフォローしているユーザーを取得
     */
    public function followers()
    {
        return $this->hasMany(Follower::class, 'follow_id', 'id');
    }

    /**
     * このユーザーがフォローしているユーザーを取得
     */
    public function follows()
    {
        return $this->hasMany(Follower::class, 'follower_id', 'id');
    }

    /**
     * 動画のコメントを取得
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * 申請済みのコインを取得
     */
    public function point_requests()
    {
        return $this->hasMany(PointRequest::class);
    }

    /**
     * ユーザーのその他のデータ
     */
    public function user_data()
    {
        return $this->hasOne(UserData::class);
    }

    // プロフィール画像のパスを取得
    public function getImagePath() {
        $path = '/images/noimage-user.jpg';
        if ($this->image) {
            $path = '/storage/users/' . $this->image;
        }
        return $path;
    }

    public function isActive(): bool
    {
        return (int)$this->account_status === self::ACCOUNT_STATUS_ACTIVE;
    }

    public function isBanned(): bool
    {
        return (int)$this->account_status === self::ACCOUNT_STATUS_BANNED;
    }
}
