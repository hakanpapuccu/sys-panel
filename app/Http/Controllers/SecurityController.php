<?php

namespace App\Http\Controllers;

use App\Models\UserSession;
use App\Support\Audit;
use App\Support\Security;
use App\Support\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecurityController extends Controller
{
    public function setupTwoFactor(Request $request, TwoFactorService $twoFactor): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        if ($user->hasTwoFactorEnabled()) {
            return back()->with('security_message', 'İki adımlı doğrulama zaten aktif.');
        }

        $secret = $twoFactor->generateSecret();
        $request->session()->put('two_factor.setup_secret', $secret);
        $request->session()->put('two_factor.setup_created_at', now()->toIso8601String());

        Security::logEvent('two_factor.setup_started', $user, [], $request);
        Audit::record('security.two_factor_setup_started', $user, [], [], [], $request);

        return back()->with('security_message', 'Kurulum anahtarı üretildi. Uygulamanızda ekleyip kodu doğrulayın.');
    }

    public function confirmTwoFactor(Request $request, TwoFactorService $twoFactor): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        $secret = (string) $request->session()->get('two_factor.setup_secret', '');
        if ($secret === '') {
            return back()->withErrors([
                'code' => 'Önce kurulum başlatılmalı.',
            ]);
        }

        if (! $twoFactor->verifyCode($secret, (string) $request->input('code'))) {
            Security::logEvent('two_factor.setup_failed', $user, [], $request);
            Audit::record('security.two_factor_setup_failed', $user, [], [], [], $request);

            return back()->withErrors([
                'code' => 'Doğrulama kodu geçersiz.',
            ]);
        }

        $recoveryCodes = $twoFactor->generateRecoveryCodes();
        $user->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => $twoFactor->hashRecoveryCodes($recoveryCodes),
        ])->save();

        $request->session()->forget('two_factor.setup_secret');
        $request->session()->forget('two_factor.setup_created_at');
        $request->session()->flash('two_factor.recovery_codes_plain', $recoveryCodes);
        $request->session()->put('auth.two_factor_verified_at', now()->toIso8601String());
        $request->session()->forget('auth.two_factor_pending');

        Security::logEvent('two_factor.enabled', $user, [
            'recovery_codes_count' => count($recoveryCodes),
        ], $request);
        Audit::record('security.two_factor_enabled', $user, [], [
            'enabled' => true,
        ], [], $request);

        return back()->with('security_message', 'İki adımlı doğrulama aktifleştirildi.');
    }

    public function disableTwoFactor(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        if (! $user->hasTwoFactorEnabled()) {
            return back()->with('security_message', 'İki adımlı doğrulama zaten pasif.');
        }

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
        ])->save();
        $request->session()->forget('auth.two_factor_pending');
        $request->session()->forget('auth.two_factor_verified_at');

        Security::logEvent('two_factor.disabled', $user, [], $request);
        Audit::record('security.two_factor_disabled', $user, [], [
            'enabled' => false,
        ], [], $request);

        return back()->with('security_message', 'İki adımlı doğrulama devre dışı bırakıldı.');
    }

    public function regenerateRecoveryCodes(Request $request, TwoFactorService $twoFactor): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        if (! $user->hasTwoFactorEnabled()) {
            return back()->with('security_message', 'Önce iki adımlı doğrulama aktif edilmelidir.');
        }

        $recoveryCodes = $twoFactor->generateRecoveryCodes();
        $user->forceFill([
            'two_factor_recovery_codes' => $twoFactor->hashRecoveryCodes($recoveryCodes),
        ])->save();
        $request->session()->flash('two_factor.recovery_codes_plain', $recoveryCodes);

        Security::logEvent('two_factor.recovery_codes_regenerated', $user, [
            'recovery_codes_count' => count($recoveryCodes),
        ], $request);
        Audit::record('security.two_factor_recovery_codes_regenerated', $user, [], [], [], $request);

        return back()->with('security_message', 'Kurtarma kodları yenilendi.');
    }

    public function revokeSession(Request $request, UserSession $session): RedirectResponse
    {
        $user = $request->user();
        if (! $user || $session->user_id !== $user->id) {
            abort(403);
        }

        if ($session->logged_out_at || $session->revoked_at) {
            return back()->with('security_message', 'Bu oturum zaten sonlandırılmış.');
        }

        $session->forceFill([
            'revoked_at' => now(),
            'revoked_by_user_id' => $user->id,
        ])->save();

        Security::logEvent('session.revoked', $user, [
            'target_session_id' => $session->session_id,
        ], $request);
        Audit::record('security.session_revoked', $user, [], [
            'target_session_id' => $session->session_id,
        ], [], $request);

        if ($session->session_id === $request->session()->getId()) {
            Security::markCurrentSessionLoggedOut($request);

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('status', 'Mevcut oturumunuz sonlandırıldı.');
        }

        return back()->with('security_message', 'Seçilen oturum sonlandırıldı.');
    }

    public function revokeOtherSessions(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        $currentSessionId = $request->session()->getId();
        $query = UserSession::query()
            ->where('user_id', $user->id)
            ->active()
            ->where('session_id', '!=', $currentSessionId);

        $affected = $query->count();
        $query->update([
            'revoked_at' => now(),
            'revoked_by_user_id' => $user->id,
        ]);

        Security::logEvent('session.revoke_others', $user, [
            'affected_sessions' => $affected,
        ], $request);
        Audit::record('security.sessions_revoked', $user, [], [
            'affected_sessions' => $affected,
        ], [], $request);

        return back()->with('security_message', "{$affected} oturum sonlandırıldı.");
    }
}
