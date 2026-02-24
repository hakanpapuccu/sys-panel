<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileShare;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Folder;

class FileShareController extends Controller
{
    public function index(Request $request)
    {
        $folderId = $request->filled('folder_id') ? (int) $request->query('folder_id') : null;
        $currentFolder = null;
        $breadcrumbs = [];
        $isAdmin = Auth::user()->is_admin;

        if ($folderId) {
            $folderQuery = Folder::query()->whereKey($folderId);
            if (! $isAdmin) {
                $folderQuery->where('user_id', Auth::id());
            }

            $currentFolder = $folderQuery->firstOrFail();

            // Build breadcrumbs
            $tempFolder = $currentFolder;
            while ($tempFolder) {
                if (! $isAdmin && $tempFolder->user_id !== Auth::id()) {
                    abort(403, 'Bu klasöre erişim yetkiniz yok.');
                }
                array_unshift($breadcrumbs, $tempFolder);
                $tempFolder = $tempFolder->parent;
            }
        }

        $foldersQuery = Folder::where('parent_id', $folderId);
        $filesQuery = FileShare::where('folder_id', $folderId)->with('user');

        if (! $isAdmin) {
            $foldersQuery->where('user_id', Auth::id());
            $filesQuery->where('user_id', Auth::id());
        }

        $folders = $foldersQuery->get();
        $files = $filesQuery->latest()->get();

        return view('files.index', compact('files', 'folders', 'currentFolder', 'breadcrumbs'));
    }

    public function create()
    {
        return view('files.create');
    }

    public function storeFolder(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
        ]);

        if (! Auth::user()->is_admin && ! empty($validated['parent_id'])) {
            $ownsParent = Folder::whereKey($validated['parent_id'])
                ->where('user_id', Auth::id())
                ->exists();

            if (! $ownsParent) {
                abort(403, 'Bu üst klasöre erişim yetkiniz yok.');
            }
        }

        Folder::create([
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'] ?? null,
            'user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Klasör oluşturuldu.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg,gif,txt,csv,zip',
            'folder_id' => 'nullable|exists:folders,id',
        ]);

        if (! Auth::user()->is_admin && ! empty($validated['folder_id'])) {
            $ownsFolder = Folder::whereKey($validated['folder_id'])
                ->where('user_id', Auth::id())
                ->exists();

            if (! $ownsFolder) {
                abort(403, 'Bu klasöre dosya yükleme yetkiniz yok.');
            }
        }

        $file = $request->file('file');
        $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_BASENAME);
        $filePath = $file->store('files', 'public');

        FileShare::create([
            'user_id' => Auth::id(),
            'folder_id' => $validated['folder_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'file_path' => $filePath,
            'file_name' => $fileName,
        ]);

        return back()->with('success', 'Dosya başarıyla yüklendi.');
    }

    public function download($id)
    {
        $file = FileShare::findOrFail($id);

        if (Auth::id() !== $file->user_id && ! Auth::user()->is_admin) {
            abort(403, 'Bu dosyayı indirme yetkiniz yok.');
        }
        
        if (Storage::disk('public')->exists($file->file_path)) {
            return Storage::disk('public')->download($file->file_path, $file->file_name);
        }

        return back()->with('error', 'Dosya bulunamadı.');
    }

    public function destroy($id)
    {
        $file = FileShare::findOrFail($id);

        if (Auth::id() !== $file->user_id && !Auth::user()->is_admin) {
            return back()->with('error', 'Bu dosyayı silme yetkiniz yok.');
        }

        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();

        return back()->with('success', 'Dosya başarıyla silindi.');
    }

    public function move(Request $request, $id)
    {
        if (!Auth::user()->is_admin) {
            return back()->with('error', 'Bu işlem için yetkiniz yok.');
        }

        $validated = $request->validate([
            'target_folder_id' => 'nullable|exists:folders,id',
        ]);

        $file = FileShare::findOrFail($id);
        $file->folder_id = $validated['target_folder_id'] ?? null;
        $file->save();

        return back()->with('success', 'Dosya başarıyla taşındı.');
    }
}
