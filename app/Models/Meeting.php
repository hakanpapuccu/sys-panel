<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'platform',
        'topic',
        'start_time',
        'duration',
        'agenda',
        'join_url',
        'join_web_url',
        'start_url',
        'meeting_id',
        'password',
    ];

    protected $casts = [
        'start_time' => 'datetime',
    ];
}
