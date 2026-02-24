<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;
use Throwable;

class ZoomService
{
    protected $accountId;
    protected $clientId;
    protected $clientSecret;
    protected $baseUrl = 'https://api.zoom.us/v2';

    public function __construct()
    {
        $this->accountId = env('ZOOM_ACCOUNT_ID');
        $this->clientId = env('ZOOM_CLIENT_ID');
        $this->clientSecret = env('ZOOM_CLIENT_SECRET');
    }

    protected function getAccessToken()
    {
        $accountId = $this->getConfig('zoom_account_id', $this->accountId);
        $clientId = $this->getConfig('zoom_client_id', $this->clientId);
        $clientSecret = $this->getConfig('zoom_client_secret', $this->clientSecret);

        if (Cache::has('zoom_access_token')) {
            return Cache::get('zoom_access_token');
        }

        $response = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => $accountId,
            ]);

        if ($response->successful()) {
            $data = $response->json();
            Cache::put('zoom_access_token', $data['access_token'], $data['expires_in'] - 60);
            return $data['access_token'];
        }

        \Illuminate\Support\Facades\Log::error('Zoom Access Token Error', [
            'status' => $response->status(),
            'error' => $response->json('reason'),
        ]);
        return null;
    }

    public function createMeeting($data)
    {
        $token = $this->getAccessToken();

        if (! $token) {
            return null;
        }

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/users/me/meetings", [
                'topic' => $data['topic'],
                'type' => 2, // Scheduled meeting
                'start_time' => $data['start_time'], // ISO 8601 format
                'duration' => $data['duration'], // Minutes
                'agenda' => $data['agenda'] ?? '',
                'settings' => [
                    'host_video' => true,
                    'participant_video' => true,
                    'join_before_host' => false,
                    'mute_upon_entry' => true,
                    'waiting_room' => true,
                ],
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        \Illuminate\Support\Facades\Log::error('Zoom Create Meeting Error', [
            'status' => $response->status(),
            'code' => $response->json('code'),
            'message' => $response->json('message'),
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
