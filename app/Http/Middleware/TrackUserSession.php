<?php

namespace App\Http\Middleware;

use App\Support\Audit;
use App\Support\Security;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackUserSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user) {
            $session = Security::ensureCurrentSession($request, $user);

            if ($session && ($session->revoked_at !== null || $session->logged_out_at !== null)) {
                Security::logEvent('session.forced_logout', $user, [
                    'session_id' => $session->session_id,
                ], $request);
                Audit::record('security.session_forced_logout', $user, [], [
                    'session_id' => $session->session_id,
                ], [], $request);

                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Oturum sonlandırıldı.'], 401);
                }

                return redirect()->route('login')->withErrors([
                    'email' => 'Oturumunuz güvenlik nedeniyle sonlandırıldı.',
                ]);
            }
        }

        $response = $next($request);

        if ($request->user()) {
            Security::ensureCurrentSession($request, $request->user());
        }

        return $response;
    }
}
