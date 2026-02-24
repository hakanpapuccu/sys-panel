<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollAnswer extends Model
{
    protected $fillable = ['poll_response_id', 'poll_question_id', 'poll_option_id', 'answer_text'];

    public function response()
    {
        return $this->belongsTo(PollResponse::class, 'poll_response_id');
    }

    public function question()
    {
        return $this->belongsTo(PollQuestion::class, 'poll_question_id');
    }

    public function option()
    {
        return $this->belongsTo(PollOption::class, 'poll_option_id');
    }
}
