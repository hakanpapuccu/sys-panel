<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacation extends Model
{
    use HasFactory;

    // Status Constants
    const STATUS_APPROVED = 1;

    const STATUS_PENDING = 2;

    const STATUS_REJECTED = 3;

    protected $fillable = [
        'vacation_user_id',
        'vacation_date',
        'vacation_start',
        'vacation_end',
        'vacation_why',
        'is_verified',
        'vacation_verifier_id',
    ];

    protected $casts = [
        'vacation_date' => 'date',
        'vacation_start' => 'datetime:H:i',
        'vacation_end' => 'datetime:H:i',
        'is_verified' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'vacation_user_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'vacation_verifier_id');
    }

    // Deprecated methods for backward compatibility (will remove later)
    public function getUser()
    {
        return $this->user();
    }

    public function getVerifier()
    {
        return $this->verifier();
    }
}
