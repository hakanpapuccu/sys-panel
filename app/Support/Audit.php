<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class Audit
{
    public static function record(
        string $event,
        Model|int|null $subject = null,
        array $oldValues = [],
        array $newValues = [],
        array $meta = [],
        ?Request $request = null
    ): void {
        $request ??= request();

        try {
            AuditLog::create([
                'user_id' => Auth::id(),
                'event' => $event,
                'auditable_type' => $subject instanceof Model ? $subject->getMorphClass() : null,
                'auditable_id' => $subject instanceof Model ? $subject->getKey() : (is_int($subject) ? $subject : null),
                'ip_address' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
                'old_values' => $oldValues ?: null,
                'new_values' => $newValues ?: null,
                'meta' => $meta ?: null,
            ]);
        } catch (Throwable $e) {
            Log::warning('Audit log write failed', [
                'event' => $event,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
