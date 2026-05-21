<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_video'      => 'boolean',
        'has_thumbnail' => 'boolean',
        'size'          => 'integer',
    ];
}
