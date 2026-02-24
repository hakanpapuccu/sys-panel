<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Support\Audit;
use App\Support\Security;
use App\Support\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TwoFactorChallengeController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->session()->get('auth.two_factor_pending')) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        return view('auth.two-factor-challenge');
    }

    public function store(Request $request, TwoFactorService $twoFactor): RedirectResponse
    {
        if (! $request->session()->get('auth.two_factor_pending')) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        $this->ensureIsNotRateLimited($request);

        $request->validate([
            'code' => ['nullable', 'string', 'required_without:recovery_code'],
            'recovery_code' => ['nullable', 'string', 'required_without:code'],
        ], [
            'code.required_without' => 'Doğrulama kodu veya kurtarma kodu giriniz.',
            'recovery_code.required_without' => 'Doğrulama kodu veya kurtarma kodu giriniz.',
        ]);

        $user = $request->user();
        if (! $user || ! $user->hasTwoFactorEnabled()) {
            $request->session()->forget('auth.two_factor_pending');

            return redirect()->intended(RouteServiceProvider::HOME);
        }

        $passed = false;
        $usedRecoveryCode = false;

        if ($request->filled('code') && $user->two_factor_secret) {
            $passed = $twoFactor->verifyCode((string) $user->two_factor_secret, (string) $request->input('code'));
        }

        if (! $passed && $request->filled('recovery_code')) {
            $updatedCodes = $twoFactor->consumeRecoveryCode(
                is_array($user->two_factor_recovery_codes) ? $user->two_factor_recovery_codes : [],
                (string) $request->input('recovery_code')
            );

            if (is_array($updatedCodes)) {
                $user->forceFill([
                    'two_factor_recovery_codes' => $updatedCodes,
                ])->save();
                $passed = true;
                $usedRecoveryCode = true;
            }
        }

        if (! $passed) {
            RateLimiter::hit($this->throttleKey($request));
            Security::logEvent('two_factor.challenge_failed', $user, [
                'remaining_attempts' => max(0, 5 - RateLimiter::attempts($this->throttleKey($request))),
            ], $request);
            Audit::record('security.two_factor_challenge_failed', $user, [], [], [
                'ip_address' => $request->ip(),
            ], $request);

            throw ValidationException::withMessages([
                'code' => 'Doğrulama kodu geçersiz.',
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));
        $request->session()->forget('auth.two_factor_pending');
        $request->session()->put('auth.two_factor_verified_at', now()->toIso8601String());

        Security::logEvent('two_factor.challenge_passed', $user, [
            'used_recovery_code' => $usedRecoveryCode,
        ], $request);
        Audit::record('security.two_factor_challenge_passed', $user, [], [
            'used_recovery_code' => $usedRecoveryCode,
        ], [], $request);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    private function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));
        throw ValidationException::withMessages([
            'code' => "Çok fazla deneme yapıldı. {$seconds} saniye sonra tekrar deneyin.",
        ]);
    }

    private function throttleKey(Request $request): string
    {
        return 'two-factor:'.$request->user()?->id.'|'.$request->ip();
    }
}
