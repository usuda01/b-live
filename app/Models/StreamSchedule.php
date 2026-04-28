<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StreamSchedule extends Model
{
    const STATUS_PUBLISHED = 1;
    const STATUS_PRIVATE = 2;
    const STATUS_CANCELLED = 3;
    const STATUS_LIVE = 4;
    const STATUS_FINISHED = 5;

    const MAX_PER_USER = 30;
    const MAX_FUTURE_MONTHS = 3;
    const REMIND_MINUTES_BEFORE = 15;

    protected $guarded = ['id'];

    protected $dates = [
        'scheduled_start_at',
        'scheduled_end_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function reminders()
    {
        return $this->hasMany(StreamScheduleReminder::class, 'schedule_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where('scheduled_start_at', '>', now());
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeBetween($query, $from, $to)
    {
        return $query->where('scheduled_start_at', '>=', $from)
            ->where('scheduled_start_at', '<', $to);
    }

    public function getThumbnailPath()
    {
        if ($this->thumbnail) {
            return '/storage/schedules/' . $this->thumbnail;
        }
        if ($this->user && $this->user->image) {
            return '/storage/users/' . $this->user->image;
        }
        return '/images/noimage-video.png';
    }

    public function isStartable(): bool
    {
        if ($this->status !== self::STATUS_PUBLISHED) {
            return false;
        }
        $window = now()->subMinutes(60);
        $tooFar = now()->addMinutes(60);
        return $this->scheduled_start_at >= $window && $this->scheduled_start_at <= $tooFar;
    }

    // 終了時刻のラベル（日跨ぎなら "翌HH:MM"）
    public function getEndTimeLabel(): ?string
    {
        if (!$this->scheduled_end_at) {
            return null;
        }
        if ($this->scheduled_end_at->isSameDay($this->scheduled_start_at)) {
            return $this->scheduled_end_at->format('H:i');
        }
        return '翌' . $this->scheduled_end_at->format('H:i');
    }
}
