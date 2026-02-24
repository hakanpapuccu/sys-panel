<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->is_admin) {
            $tasks = Task::with(['assignedTo', 'createdBy'])->orderBy('deadline')->get();
        } else {
            $tasks = Task::with(['assignedTo', 'createdBy'])
                ->where('assigned_to_id', $user->id)
                ->orWhere('created_by_id', $user->id)
                ->orderBy('deadline')
                ->get();
        }
        
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $users = User::all();
        return view('tasks.create', compact('users'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Task::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'assigned_to_id' => 'required|exists:users,id',
        ]);

        $validated['description'] = $this->sanitizeDescription($validated['description'] ?? null);
        $validated['created_by_id'] = Auth::id();
        $validated['status'] = 'pending';

        $task = Task::create($validated);

        // Notify Assigned User
        $task->assignedTo->notify(new \App\Notifications\TaskAssigned($task));

        session()->flash('success', 'Görev başarıyla oluşturuldu');
        return redirect()->route('tasks.index');
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        $users = User::all();
        return view('tasks.edit', compact('task', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        if (!Auth::user()->is_admin) {
            $validated = $request->validate([
                'status' => 'required|in:pending,in_progress,completed',
            ]);
            // Prevent non-admins from changing other fields
            $task->update(['status' => $validated['status']]);
        } else {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'deadline' => 'nullable|date',
                'priority' => 'required|in:low,medium,high',
                'status' => 'required|in:pending,in_progress,completed',
                'assigned_to_id' => 'required|exists:users,id',
            ]);
            $validated['description'] = $this->sanitizeDescription($validated['description'] ?? null);
            $task->update($validated);
        }

        session()->flash('success', 'Görev güncellendi');
        return redirect()->route('tasks.index');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        session()->flash('success', 'Görev silindi');
        return redirect()->route('tasks.index');
    }

    private function sanitizeDescription(?string $description): ?string
    {
        if ($description === null) {
            return null;
        }

        $description = trim(strip_tags($description));

        return $description === '' ? null : $description;
    }
}
