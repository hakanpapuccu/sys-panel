<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with(['user', 'comments.user'])->latest()->get();
        return view('announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $content = $this->sanitizeContent($request->input('content'));
        if ($content === '' || mb_strlen($content) > 1000) {
            return back()
                ->withErrors(['content' => 'Duyuru metni 1-1000 karakter arasında olmalıdır.'])
                ->withInput();
        }

        Announcement::create([
            'user_id' => auth()->id(),
            'content' => $content,
        ]);

        return back()->with('success', 'Duyuru paylaşıldı.');
    }

    public function edit(Announcement $announcement)
    {
        if ($announcement->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }
        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        if ($announcement->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $content = $this->sanitizeContent($request->input('content'));
        if ($content === '' || mb_strlen($content) > 1000) {
            return back()
                ->withErrors(['content' => 'Duyuru metni 1-1000 karakter arasında olmalıdır.'])
                ->withInput();
        }

        $announcement->update([
            'content' => $content,
        ]);

        return redirect()->route('announcements.index')->with('success', 'Duyuru güncellendi.');
    }

    public function destroy(Announcement $announcement)
    {
        if ($announcement->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        $announcement->delete();

        return back()->with('success', 'Duyuru silindi.');
    }

    private function sanitizeContent(string $content): string
    {
        return trim(strip_tags($content));
    }
}
