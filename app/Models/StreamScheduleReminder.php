<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StreamScheduleReminder extends Model
{
    protected $guarded = ['id'];

    protected $dates = [
        'notified_at',
    ];

    public function schedule()
    {
        return $this->belongsTo(StreamSchedule::class, 'schedule_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
