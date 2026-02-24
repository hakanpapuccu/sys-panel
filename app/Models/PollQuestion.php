<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollQuestion extends Model
{
    protected $fillable = ['poll_id', 'type', 'question', 'order'];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function options()
    {
        return $this->hasMany(PollOption::class);
    }

    public function answers()
    {
        return $this->hasMany(PollAnswer::class);
    }
}
