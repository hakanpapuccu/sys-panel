<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class TeamsService
{
    protected $tenantId;
    protected $clientId;
    protected $clientSecret;
    protected $userId;
    protected $baseUrl = 'https://graph.microsoft.com/v1.0';

    public function __construct()
    {
        $this->tenantId = Setting::get('teams_tenant_id', env('TEAMS_TENANT_ID'));
        $this->clientId = Setting::get('teams_client_id', env('TEAMS_CLIENT_ID'));
        $this->clientSecret = Setting::get('teams_client_secret', env('TEAMS_CLIENT_SECRET'));
        $this->userId = Setting::get('teams_user_id', env('TEAMS_USER_ID'));
    }

    protected function getAccessToken()
    {
        if (Cache::has('teams_access_token')) {
            return Cache::get('teams_access_token');
        }

        $response = Http::asForm()->post("https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token", [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope' => 'https://graph.microsoft.com/.default',
            'grant_type' => 'client_credentials',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            Cache::put('teams_access_token', $data['access_token'], $data['expires_in'] - 60);
            return $data['access_token'];
        }

        Log::error('Teams Access Token Error: ' . $response->body());
        return null;
    }

    public function createMeeting($data)
    {
        $token = $this->getAccessToken();

        if (! $token) {
            return null;
        }

        if (! $this->userId) {
            Log::error('Teams User ID not configured');
            return null;
        }

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/users/{$this->userId}/onlineMeetings", [
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

        Log::error('Teams Create Meeting Error: ' . $response->body());
        return null;
    }
}
