<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\User;
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

    public function store(StoreTaskRequest $request)
    {
        $this->authorize('create', Task::class);

        $validated = $request->validated();

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

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        if (! Auth::user()->is_admin) {
            $validated = $request->validated();
            // Prevent non-admins from changing other fields
            $task->update(['status' => $validated['status']]);
        } else {
            $validated = $request->validated();
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
