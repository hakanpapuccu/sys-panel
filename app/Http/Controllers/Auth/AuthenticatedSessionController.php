<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Support\Audit;
use App\Support\Security;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        if ($user) {
            Security::ensureCurrentSession($request, $user);
            Security::logEvent('login.success', $user, [
                'session_id' => $request->session()->getId(),
            ], $request);
            Audit::record('auth.login', $user, [], [
                'session_id' => $request->session()->getId(),
            ], [], $request);

            if ($user->hasTwoFactorEnabled()) {
                $request->session()->put('auth.two_factor_pending', true);
                $request->session()->forget('auth.two_factor_verified_at');
                Security::logEvent('two_factor.challenge_required', $user, [], $request);
                Audit::record('security.two_factor_challenge_required', $user, [], [], [], $request);

                return redirect()->route('two-factor.challenge');
            }
        }

        $request->session()->forget('auth.two_factor_pending');
        $request->session()->put('auth.two_factor_verified_at', now()->toIso8601String());

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user) {
            Security::logEvent('logout', $user, [
                'session_id' => $request->session()->getId(),
            ], $request);
            Audit::record('auth.logout', $user, [], [
                'session_id' => $request->session()->getId(),
            ], [], $request);
            Security::markCurrentSessionLoggedOut($request);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
