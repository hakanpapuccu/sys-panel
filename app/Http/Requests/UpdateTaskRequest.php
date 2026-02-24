<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $task = $this->route('task');

        return $user !== null
            && $task instanceof Task
            && $user->can('update', $task);
    }

    public function rules(): array
    {
        if (! $this->user()?->is_admin) {
            return [
                'status' => ['required', 'in:pending,in_progress,completed'],
            ];
        }

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'deadline' => ['nullable', 'date'],
            'priority' => ['required', 'in:low,medium,high'],
            'status' => ['required', 'in:pending,in_progress,completed'],
            'assigned_to_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
