<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->hasPermission('manage_platform_settings');
    }

    public function rules(): array
    {
        return [
            'site_title' => ['nullable', 'string', 'max:255'],
            'site_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'site_favicon' => ['nullable', 'image', 'mimes:ico,png', 'max:1024'],
            'zoom_account_id' => ['nullable', 'string', 'max:255'],
            'zoom_client_id' => ['nullable', 'string', 'max:255'],
            'zoom_client_secret' => ['nullable', 'string', 'max:255'],
            'teams_tenant_id' => ['nullable', 'string', 'max:255'],
            'teams_client_id' => ['nullable', 'string', 'max:255'],
            'teams_client_secret' => ['nullable', 'string', 'max:255'],
            'teams_user_id' => ['nullable', 'string', 'max:255'],
        ];
    }
}
