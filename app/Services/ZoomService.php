<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

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
        if (Cache::has('zoom_access_token')) {
            return Cache::get('zoom_access_token');
        }

        $response = Http::asForm()
            ->withBasicAuth($this->clientId, $this->clientSecret)
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => $this->accountId,
            ]);

        if ($response->successful()) {
            $data = $response->json();
            Cache::put('zoom_access_token', $data['access_token'], $data['expires_in'] - 60);
            return $data['access_token'];
        }

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

        return null;
    }
}
