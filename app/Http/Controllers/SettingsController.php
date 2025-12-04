<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_title' => 'nullable|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'site_favicon' => 'nullable|image|mimes:ico,png|max:1024',
            'zoom_account_id' => 'nullable|string|max:255',
            'zoom_client_id' => 'nullable|string|max:255',
            'zoom_client_secret' => 'nullable|string|max:255',
            'teams_tenant_id' => 'nullable|string|max:255',
            'teams_client_id' => 'nullable|string|max:255',
            'teams_client_secret' => 'nullable|string|max:255',
            'teams_user_id' => 'nullable|string|max:255',
        ]);

        if ($request->has('site_title')) {
            Setting::set('site_title', $request->site_title);
        }

        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('public/settings');
            Setting::set('site_logo', str_replace('public/', '', $path));
        }

        if ($request->hasFile('site_favicon')) {
            $path = $request->file('site_favicon')->store('public/settings');
            Setting::set('site_favicon', str_replace('public/', '', $path));
        }

        if ($request->has('zoom_account_id')) {
            Setting::set('zoom_account_id', $request->zoom_account_id);
        }

        if ($request->has('zoom_client_id')) {
            Setting::set('zoom_client_id', $request->zoom_client_id);
        }

        if ($request->has('zoom_client_secret')) {
            Setting::set('zoom_client_secret', $request->zoom_client_secret);
        }

        if ($request->has('teams_tenant_id')) {
            Setting::set('teams_tenant_id', $request->teams_tenant_id);
        }

        if ($request->has('teams_client_id')) {
            Setting::set('teams_client_id', $request->teams_client_id);
        }

        if ($request->has('teams_client_secret')) {
            Setting::set('teams_client_secret', $request->teams_client_secret);
        }

        if ($request->has('teams_user_id')) {
            Setting::set('teams_user_id', $request->teams_user_id);
        }

        return redirect()->back()->with('success', 'Ayarlar güncellendi.');
    }
}
