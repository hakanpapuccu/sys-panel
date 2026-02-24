<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\AuditLog;
use App\Models\SecurityEvent;
use App\Models\UserSession;
use App\Support\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Throwable;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request, $userId = null): View
    {
        // If userId is provided and user is admin, load that user
        // Otherwise, load the authenticated user
        if ($userId && $request->user()->is_admin) {
            $user = \App\Models\User::findOrFail($userId);
        } else {
            $user = $request->user();
        }

        $isEditingOtherUser = $userId !== null && $request->user()->id !== $user->id;
        $showSecurityPanel = ! $isEditingOtherUser;

        $activeSessions = collect();
        $securityEvents = collect();
        $criticalAuditLogs = collect();
        $currentSessionId = null;
        $pendingTwoFactorSecret = null;
        $pendingTwoFactorOtpAuthUrl = null;

        if ($showSecurityPanel) {
            try {
                $currentUser = $request->user();
                $currentSessionId = $request->session()->getId();

                $activeSessions = UserSession::query()
                    ->where('user_id', $currentUser->id)
                    ->active()
                    ->orderByDesc('last_activity_at')
                    ->limit(20)
                    ->get();

                $securityEvents = SecurityEvent::query()
                    ->where(function ($query) use ($currentUser) {
                        $query->where('user_id', $currentUser->id)
                            ->orWhere(function ($innerQuery) use ($currentUser) {
                                $innerQuery->whereNull('user_id')
                                    ->where('context->email', $currentUser->email);
                            });
                    })
                    ->orderByDesc('created_at')
                    ->limit(30)
                    ->get();

                $criticalAuditLogs = AuditLog::query()
                    ->where('user_id', $currentUser->id)
                    ->where(function ($query) {
                        $query->where('event', 'like', 'auth.%')
                            ->orWhere('event', 'like', 'security.%');
                    })
                    ->latest()
                    ->limit(30)
                    ->get();

                $pendingTwoFactorSecret = (string) $request->session()->get('two_factor.setup_secret', '');
                if ($pendingTwoFactorSecret !== '') {
                    $pendingTwoFactorOtpAuthUrl = app(TwoFactorService::class)
                        ->otpAuthUrl($currentUser, $pendingTwoFactorSecret);
                }
            } catch (Throwable) {
                $activeSessions = collect();
                $securityEvents = collect();
                $criticalAuditLogs = collect();
                $currentSessionId = $request->session()->getId();
            }
        }

        return view('profile.edit', [
            'user' => $user,
            'isEditingOtherUser' => $isEditingOtherUser,
            'showSecurityPanel' => $showSecurityPanel,
            'activeSessions' => $activeSessions,
            'securityEvents' => $securityEvents,
            'criticalAuditLogs' => $criticalAuditLogs,
            'currentSessionId' => $currentSessionId,
            'pendingTwoFactorSecret' => $pendingTwoFactorSecret,
            'pendingTwoFactorOtpAuthUrl' => $pendingTwoFactorOtpAuthUrl,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Check if we're editing another user (admin only)
        $userId = $request->input('user_id');
        if ($userId && $request->user()->is_admin) {
            $user = \App\Models\User::findOrFail($userId);
        } else {
            $user = $request->user();
        }

        // Note: Image upload is now handled by uploadImage method,
        // but we keep this logic here if we want to support it in the main form too,
        // or we can remove it. For now, I'll leave it but the main form won't send these fields.

        // Handle is_admin field (only if admin is editing another user)
        if ($request->user()->is_admin && $userId) {
            $user->is_admin = $request->has('is_admin');
        }

        // Remove is_admin from data before filling to avoid mass assignment issues
        unset($data['is_admin'], $data['user_id']);

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('profile.edit', $userId ?: null)->with('status', 'profile-updated');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'profile_image' => ['nullable', 'image', 'max:2048'],
            'banner_image' => ['nullable', 'image', 'max:2048'],
        ]);

        // Check if we're editing another user (admin only)
        $userId = $request->input('user_id');
        if ($userId && $request->user()->is_admin) {
            $user = \App\Models\User::findOrFail($userId);
        } else {
            $user = $request->user();
        }

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                // Storage::disk('public')->delete($user->profile_image);
            }
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        if ($request->hasFile('banner_image')) {
            if ($user->banner_image) {
                // Storage::disk('public')->delete($user->banner_image);
            }
            $path = $request->file('banner_image')->store('banner_images', 'public');
            $user->banner_image = $path;
        }

        $user->save();

        return back()->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
