<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MeetingController extends Controller
{
    protected $zoomService;
    protected $teamsService;

    public function __construct(ZoomService $zoomService, \App\Services\TeamsService $teamsService)
    {
        $this->zoomService = $zoomService;
        $this->teamsService = $teamsService;
    }

    public function index()
    {
        $meetings = Meeting::where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->paginate(10);
        return view('meetings.index', compact('meetings'));
    }

    public function adminIndex()
    {
        $meetings = Meeting::orderBy('start_time', 'desc')->paginate(10);
        return view('admin.meetings.index', compact('meetings'));
    }

    public function create()
    {
        return view('admin.meetings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'start_time' => 'required|date|after:now',
            'duration' => 'required|integer|min:1',
            'agenda' => 'nullable|string',
            'platform' => 'required|in:zoom,teams',
        ]);

        $startTime = Carbon::parse($request->start_time);
        
        $meetingData = [
            'topic' => $request->topic,
            'start_time' => $startTime->toIso8601String(),
            'end_time' => $startTime->copy()->addMinutes($request->duration)->toIso8601String(),
            'duration' => $request->duration,
            'agenda' => $request->agenda,
        ];

        $platform = $request->platform;
        $meetingResult = null;

        if ($platform === 'zoom') {
            $meetingResult = $this->zoomService->createMeeting($meetingData);
        } elseif ($platform === 'teams') {
            $meetingResult = $this->teamsService->createMeeting($meetingData);
        }

        if ($meetingResult) {
            $joinUrl = $platform === 'teams'
                ? ($meetingResult['joinWebUrl'] ?? null)
                : ($meetingResult['join_url'] ?? null);
            $startUrl = $platform === 'teams'
                ? null
                : ($meetingResult['start_url'] ?? null);

            Meeting::create([
                'platform' => $platform,
                'topic' => $request->topic,
                'start_time' => $startTime,
                'duration' => $request->duration,
                'agenda' => $request->agenda,
                'join_url' => $this->sanitizeExternalUrl($joinUrl),
                'join_web_url' => $platform === 'teams' ? $this->sanitizeExternalUrl($joinUrl) : null,
                'start_url' => $this->sanitizeExternalUrl($startUrl),
                'meeting_id' => isset($meetingResult['id']) ? (string) $meetingResult['id'] : null,
                'password' => $platform === 'zoom' ? ($meetingResult['password'] ?? null) : null,
            ]);

            return redirect()->route('admin.meetings.index')->with('success', 'Toplantı başarıyla oluşturuldu.');
        }

        return back()->with('error', ucfirst($platform) . ' toplantısı oluşturulamadı. Lütfen API ayarlarını kontrol edin.');
    }

    public function destroy(Meeting $meeting)
    {
        // Optionally delete from Zoom as well
        $meeting->delete();
        return redirect()->route('admin.meetings.index')->with('success', 'Toplantı silindi.');
    }

    private function sanitizeExternalUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $url = trim($url);
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        if (! in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        return $url;
    }
}
