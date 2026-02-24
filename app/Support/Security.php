<?php

namespace App\Support;

use App\Models\SecurityEvent;
use App\Models\User;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class Security
{
    public static function logEvent(
        string $event,
        ?User $user = null,
        array $context = [],
        ?Request $request = null
    ): void {
        $request ??= request();

        try {
            SecurityEvent::create([
                'user_id' => $user?->id,
                'event' => $event,
                'ip_address' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
                'context' => $context ?: null,
            ]);
        } catch (Throwable $e) {
            Log::warning('Security event write failed', [
                'event' => $event,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public static function ensureCurrentSession(Request $request, User $user): ?UserSession
    {
        $sessionId = $request->session()->getId();
        if (! $sessionId) {
            return null;
        }

        try {
            $session = UserSession::query()->firstOrCreate(
                ['session_id' => $sessionId],
                [
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'login_at' => now(),
                    'last_activity_at' => now(),
                ]
            );

            if ($session->user_id !== $user->id) {
                $session->user_id = $user->id;
            }
            $session->ip_address = $request->ip();
            $session->user_agent = $request->userAgent();
            $session->last_activity_at = now();
            if (! $session->login_at) {
                $session->login_at = now();
            }
            $session->save();

            $request->session()->put('security.session_record_id', $session->id);

            return $session;
        } catch (Throwable $e) {
            Log::warning('Session tracking failed', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public static function markCurrentSessionLoggedOut(Request $request): void
    {
        $sessionId = $request->session()->getId();
        if (! $sessionId) {
            return;
        }

        try {
            UserSession::query()
                ->where('session_id', $sessionId)
                ->whereNull('logged_out_at')
                ->update([
                    'logged_out_at' => now(),
                    'last_activity_at' => now(),
                ]);
        } catch (Throwable $e) {
            Log::warning('Session logout marker failed', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
