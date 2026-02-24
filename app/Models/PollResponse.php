<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollResponse extends Model
{
    protected $fillable = ['poll_id', 'user_id'];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(PollAnswer::class);
    }
}
