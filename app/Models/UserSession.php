<?php

namespace App\Models;

use App\Support\UserAgentParser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'login_at',
        'last_activity_at',
        'logged_out_at',
        'revoked_at',
        'revoked_by_user_id',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'logged_out_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function revokedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by_user_id');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('logged_out_at')->whereNull('revoked_at');
    }

    public function getDeviceLabelAttribute(): string
    {
        return UserAgentParser::summarize($this->user_agent);
    }
}
