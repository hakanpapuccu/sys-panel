<?php

namespace App\Http\Controllers;

use App\Http\Requests\MoveFileRequest;
use App\Http\Requests\StoreFileShareRequest;
use App\Http\Requests\StoreFolderRequest;
use App\Models\FileShare;
use App\Models\Folder;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function storeFolder(StoreFolderRequest $request)
    {
        $this->authorize('createFolder', FileShare::class);
        $validated = $request->validated();

        if (! Auth::user()->is_admin && ! empty($validated['parent_id'])) {
            $ownsParent = Folder::whereKey($validated['parent_id'])
                ->where('user_id', Auth::id())
                ->exists();

            if (! $ownsParent) {
                abort(403, 'Bu üst klasöre erişim yetkiniz yok.');
            }
        }

        $folder = Folder::create([
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'] ?? null,
            'user_id' => Auth::id(),
        ]);

        Audit::record('files.folder.created', $folder, [], [
            'name' => $folder->name,
            'parent_id' => $folder->parent_id,
            'user_id' => $folder->user_id,
        ]);

        return back()->with('success', 'Klasör oluşturuldu.');
    }

    public function store(StoreFileShareRequest $request)
    {
        $this->authorize('create', FileShare::class);
        $validated = $request->validated();

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
        $filePath = $file->store('files', 'private');

        $uploadedFile = FileShare::create([
            'user_id' => Auth::id(),
            'folder_id' => $validated['folder_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'file_path' => $filePath,
            'storage_disk' => 'private',
            'file_name' => $fileName,
        ]);

        Audit::record('files.uploaded', $uploadedFile, [], [
            'title' => $uploadedFile->title,
            'folder_id' => $uploadedFile->folder_id,
            'storage_disk' => $uploadedFile->storage_disk,
            'file_path' => $uploadedFile->file_path,
        ]);

        return back()->with('success', 'Dosya başarıyla yüklendi.');
    }

    public function download($id)
    {
        $file = FileShare::findOrFail($id);
        $this->authorize('download', $file);

        $disk = $this->resolveStorageDisk($file);
        if (! $disk || ! Storage::disk($disk)->exists($file->file_path)) {
            return back()->with('error', 'Dosya bulunamadı.');
        }

        return Storage::disk($disk)->download($file->file_path, $file->file_name);
    }

    public function preview($id): StreamedResponse
    {
        $file = FileShare::findOrFail($id);
        $this->authorize('preview', $file);

        $disk = $this->resolveStorageDisk($file);
        if (! $disk || ! Storage::disk($disk)->exists($file->file_path)) {
            abort(404, 'Dosya bulunamadı.');
        }

        $ext = strtolower((string) pathinfo($file->file_name, PATHINFO_EXTENSION));
        $previewableExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'txt', 'csv'];
        if (! in_array($ext, $previewableExtensions, true)) {
            abort(404, 'Bu dosya türü için önizleme desteklenmiyor.');
        }

        $stream = Storage::disk($disk)->readStream($file->file_path);
        if (! is_resource($stream)) {
            abort(404, 'Dosya okunamadı.');
        }

        $mimeType = Storage::disk($disk)->mimeType($file->file_path) ?: 'application/octet-stream';

        return response()->stream(function () use ($stream): void {
            fpassthru($stream);
            fclose($stream);
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.addslashes($file->file_name).'"',
            'Cache-Control' => 'private, max-age=300',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function destroy($id)
    {
        $file = FileShare::findOrFail($id);
        $this->authorize('delete', $file);
        $oldValues = [
            'title' => $file->title,
            'folder_id' => $file->folder_id,
            'storage_disk' => $file->storage_disk,
            'file_path' => $file->file_path,
            'user_id' => $file->user_id,
        ];

        $disk = $this->resolveStorageDisk($file);
        if ($disk && Storage::disk($disk)->exists($file->file_path)) {
            Storage::disk($disk)->delete($file->file_path);
        }

        $file->delete();
        Audit::record('files.deleted', null, $oldValues, ['deleted_file_id' => $file->id]);

        return back()->with('success', 'Dosya başarıyla silindi.');
    }

    public function move(MoveFileRequest $request, $id)
    {
        $file = FileShare::findOrFail($id);
        $this->authorize('move', $file);
        $oldValues = [
            'folder_id' => $file->folder_id,
        ];
        $validated = $request->validated();
        $file->folder_id = $validated['target_folder_id'] ?? null;
        $file->save();
        Audit::record('files.moved', $file, $oldValues, [
            'folder_id' => $file->folder_id,
        ]);

        return back()->with('success', 'Dosya başarıyla taşındı.');
    }

    private function resolveStorageDisk(FileShare $file): ?string
    {
        $candidates = array_filter([
            $file->storage_disk,
            'private',
            'public',
        ]);

        foreach (array_unique($candidates) as $disk) {
            if (! config("filesystems.disks.{$disk}")) {
                continue;
            }

            if (Storage::disk($disk)->exists($file->file_path)) {
                return $disk;
            }
        }

        if ($file->storage_disk && config("filesystems.disks.{$file->storage_disk}")) {
            return $file->storage_disk;
        }

        return null;
    }
}
