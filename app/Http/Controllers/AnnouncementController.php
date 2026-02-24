<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Requests\UpdateAnnouncementRequest;
use App\Models\Announcement;
use App\Support\Audit;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with(['user', 'comments.user'])->latest()->get();

        return view('announcements.index', compact('announcements'));
    }

    public function store(StoreAnnouncementRequest $request)
    {
        $this->authorize('create', Announcement::class);
        $content = $this->sanitizeContent($request->input('content'));
        if ($content === '' || mb_strlen($content) > 1000) {
            return back()
                ->withErrors(['content' => 'Duyuru metni 1-1000 karakter arasında olmalıdır.'])
                ->withInput();
        }

        $announcement = Announcement::create([
            'user_id' => auth()->id(),
            'content' => $content,
        ]);
        Audit::record('announcement.created', $announcement, [], [
            'user_id' => $announcement->user_id,
            'content_length' => mb_strlen($announcement->content),
        ]);

        return back()->with('success', 'Duyuru paylaşıldı.');
    }

    public function edit(Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        return view('announcements.edit', compact('announcement'));
    }

    public function update(UpdateAnnouncementRequest $request, Announcement $announcement)
    {
        $this->authorize('update', $announcement);
        $oldValues = [
            'content' => $announcement->content,
        ];

        $content = $this->sanitizeContent($request->input('content'));
        if ($content === '' || mb_strlen($content) > 1000) {
            return back()
                ->withErrors(['content' => 'Duyuru metni 1-1000 karakter arasında olmalıdır.'])
                ->withInput();
        }

        $announcement->update([
            'content' => $content,
        ]);
        Audit::record('announcement.updated', $announcement, $oldValues, [
            'content' => $announcement->content,
        ]);

        return redirect()->route('announcements.index')->with('success', 'Duyuru güncellendi.');
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorize('delete', $announcement);
        $oldValues = [
            'content' => $announcement->content,
            'user_id' => $announcement->user_id,
        ];
        $announcement->delete();
        Audit::record('announcement.deleted', null, $oldValues, [
            'deleted_announcement_id' => $announcement->id,
        ]);

        return back()->with('success', 'Duyuru silindi.');
    }

    private function sanitizeContent(string $content): string
    {
        return trim(strip_tags($content));
    }
}
