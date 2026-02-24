<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        if (! $user->hasTwoFactorEnabled()) {
            $request->session()->forget('auth.two_factor_pending');
            $request->session()->forget('auth.two_factor_verified_at');

            return $next($request);
        }

        if (! $request->session()->has('auth.two_factor_verified_at')) {
            $request->session()->put('auth.two_factor_pending', true);
        }

        if (! $request->session()->get('auth.two_factor_pending')) {
            return $next($request);
        }

        if ($request->routeIs('two-factor.challenge', 'two-factor.verify', 'logout')) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'İki adımlı doğrulama gerekli.'], 423);
        }

        return redirect()->route('two-factor.challenge');
    }
}
