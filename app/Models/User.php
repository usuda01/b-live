<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

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
     * ブロックしたユーザーを取得
     */
    public function blockUsers()
    {
        return $this->hasMany('App\Block', 'blocker_id', 'id');
    }

    /**
     * このユーザーをフォローしているユーザーを取得
     */
    public function followers()
    {
        return $this->hasMany('App\Follower', 'follow_id', 'id');
    }

    /**
     * このユーザーがフォローしているユーザーを取得
     */
    public function follows()
    {
        return $this->hasMany('App\Follower', 'follower_id', 'id');
    }

    /**
     * 動画のコメントを取得
     */
    public function messages()
    {
        return $this->hasMany('App\Message');
    }

    /**
     * 申請済みのコインを取得
     */
    public function point_requests()
    {
        return $this->hasMany('App\PointRequest');
    }

    /**
     * ユーザーのその他のデータ
     */
    public function user_data()
    {
        return $this->hasOne('App\UserData');
    }

    // プロフィール画像のパスを取得
    public function getImagePath() {
        $path = '/images/noimage-user.jpg';
        if ($this->image) {
            $path = '/storage/users/' . $this->image;
        }
        return $path;
    }
}
