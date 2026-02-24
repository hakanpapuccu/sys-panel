<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use App\Support\Audit;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(UpdateSettingsRequest $request)
    {
        $updatedKeys = [];

        if ($request->has('site_title')) {
            Setting::set('site_title', $request->site_title);
            $updatedKeys[] = 'site_title';
        }

        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('public/settings');
            Setting::set('site_logo', str_replace('public/', '', $path));
            $updatedKeys[] = 'site_logo';
        }

        if ($request->hasFile('site_favicon')) {
            $path = $request->file('site_favicon')->store('public/settings');
            Setting::set('site_favicon', str_replace('public/', '', $path));
            $updatedKeys[] = 'site_favicon';
        }

        if ($request->has('zoom_account_id')) {
            Setting::set('zoom_account_id', $request->zoom_account_id);
            $updatedKeys[] = 'zoom_account_id';
        }

        if ($request->has('zoom_client_id')) {
            Setting::set('zoom_client_id', $request->zoom_client_id);
            $updatedKeys[] = 'zoom_client_id';
        }

        if ($request->has('zoom_client_secret')) {
            Setting::set('zoom_client_secret', $request->zoom_client_secret);
            $updatedKeys[] = 'zoom_client_secret';
        }

        if ($request->has('teams_tenant_id')) {
            Setting::set('teams_tenant_id', $request->teams_tenant_id);
            $updatedKeys[] = 'teams_tenant_id';
        }

        if ($request->has('teams_client_id')) {
            Setting::set('teams_client_id', $request->teams_client_id);
            $updatedKeys[] = 'teams_client_id';
        }

        if ($request->has('teams_client_secret')) {
            Setting::set('teams_client_secret', $request->teams_client_secret);
            $updatedKeys[] = 'teams_client_secret';
        }

        if ($request->has('teams_user_id')) {
            Setting::set('teams_user_id', $request->teams_user_id);
            $updatedKeys[] = 'teams_user_id';
        }

        if ($updatedKeys !== []) {
            Audit::record('settings.updated', null, [], [], [
                'updated_keys' => $updatedKeys,
            ]);
        }

        return redirect()->back()->with('success', 'Ayarlar güncellendi.');
    }
}
