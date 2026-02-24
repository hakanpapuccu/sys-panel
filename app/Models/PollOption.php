<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    protected $fillable = ['poll_question_id', 'option_text'];

    public function question()
    {
        return $this->belongsTo(PollQuestion::class, 'poll_question_id');
    }
}
