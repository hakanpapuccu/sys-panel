<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserSession;
use App\Support\Audit;
use App\Support\Security;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $currentSessionId = $request->session()->getId();
        $affected = UserSession::query()
            ->where('user_id', $request->user()->id)
            ->active()
            ->where('session_id', '!=', $currentSessionId)
            ->update([
                'revoked_at' => now(),
                'revoked_by_user_id' => $request->user()->id,
            ]);

        Security::logEvent('password.changed', $request->user(), [
            'revoked_sessions' => $affected,
        ], $request);
        Audit::record('security.password_changed', $request->user(), [], [
            'revoked_sessions' => $affected,
        ], [], $request);

        return back()->with('status', 'password-updated');
    }
}
