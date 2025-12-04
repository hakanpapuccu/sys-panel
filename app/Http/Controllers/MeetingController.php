<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MeetingController extends Controller
{
    protected $zoomService;

    public function __construct(ZoomService $zoomService)
    {
        $this->zoomService = $zoomService;
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
        ]);

        $startTime = Carbon::parse($request->start_time);
        
        $zoomData = [
            'topic' => $request->topic,
            'start_time' => $startTime->toIso8601String(),
            'duration' => $request->duration,
            'agenda' => $request->agenda,
        ];

        $zoomMeeting = $this->zoomService->createMeeting($zoomData);

        if ($zoomMeeting) {
            Meeting::create([
                'topic' => $request->topic,
                'start_time' => $startTime,
                'duration' => $request->duration,
                'agenda' => $request->agenda,
                'join_url' => $zoomMeeting['join_url'],
                'start_url' => $zoomMeeting['start_url'],
                'meeting_id' => $zoomMeeting['id'],
                'password' => $zoomMeeting['password'] ?? null,
            ]);

            return redirect()->route('admin.meetings.index')->with('success', 'Toplantı başarıyla oluşturuldu.');
        }

        // Fallback if Zoom fails (optional: create local only or show error)
        // For now, we'll show an error.
        return back()->with('error', 'Zoom toplantısı oluşturulamadı. Lütfen API ayarlarını kontrol edin.');
    }

    public function destroy(Meeting $meeting)
    {
        // Optionally delete from Zoom as well
        $meeting->delete();
        return redirect()->route('admin.meetings.index')->with('success', 'Toplantı silindi.');
    }
}
