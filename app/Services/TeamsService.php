<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TeamsService
{
    protected $tenantId;

    protected $clientId;

    protected $clientSecret;

    protected $userId;

    protected $baseUrl = 'https://graph.microsoft.com/v1.0';

    public function __construct()
    {
        $this->tenantId = env('TEAMS_TENANT_ID');
        $this->clientId = env('TEAMS_CLIENT_ID');
        $this->clientSecret = env('TEAMS_CLIENT_SECRET');
        $this->userId = env('TEAMS_USER_ID');
    }

    protected function getAccessToken()
    {
        $tenantId = $this->getConfig('teams_tenant_id', $this->tenantId);
        $clientId = $this->getConfig('teams_client_id', $this->clientId);
        $clientSecret = $this->getConfig('teams_client_secret', $this->clientSecret);

        if (Cache::has('teams_access_token')) {
            return Cache::get('teams_access_token');
        }

        $response = Http::asForm()->post("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token", [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => 'https://graph.microsoft.com/.default',
            'grant_type' => 'client_credentials',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            Cache::put('teams_access_token', $data['access_token'], $data['expires_in'] - 60);

            return $data['access_token'];
        }

        Log::error('Teams Access Token Error', [
            'status' => $response->status(),
            'error' => $response->json('error'),
            'error_description' => $response->json('error_description'),
        ]);

        return null;
    }

    public function createMeeting($data)
    {
        $token = $this->getAccessToken();
        $userId = $this->getConfig('teams_user_id', $this->userId);

        if (! $token) {
            return null;
        }

        if (! $userId) {
            Log::error('Teams User ID not configured');

            return null;
        }

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/users/{$userId}/onlineMeetings", [
                'startDateTime' => $data['start_time'], // ISO 8601
                'endDateTime' => $data['end_time'],   // ISO 8601
                'subject' => $data['topic'],
                'lobbyBypassSettings' => [
                    'scope' => 'everyone',
                ],
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Teams Create Meeting Error', [
            'status' => $response->status(),
            'error' => $response->json('error'),
            'message' => $response->json('error.message'),
        ]);

        return null;
    }

    private function getConfig(string $key, ?string $default = null): ?string
    {
        try {
            return Setting::get($key, $default);
        } catch (Throwable $e) {
            return $default;
        }
    }
}
