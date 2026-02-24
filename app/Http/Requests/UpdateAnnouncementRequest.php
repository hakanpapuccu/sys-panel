<?php

namespace App\Http\Requests;

use App\Models\Announcement;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $announcement = $this->route('announcement');

        return $user !== null
            && $announcement instanceof Announcement
            && $user->can('update', $announcement);
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:5000'],
        ];
    }
}
