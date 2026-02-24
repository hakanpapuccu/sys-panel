<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    protected $fillable = ['title', 'description', 'start_date', 'end_date', 'is_active', 'created_by'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(PollQuestion::class)->orderBy('order');
    }

    public function responses()
    {
        return $this->hasMany(PollResponse::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
