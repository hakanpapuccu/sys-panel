<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MoveFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->is_admin && $user->hasPermission('delete_files');
    }

    public function rules(): array
    {
        return [
            'target_folder_id' => ['nullable', 'integer', 'exists:folders,id'],
        ];
    }
}
