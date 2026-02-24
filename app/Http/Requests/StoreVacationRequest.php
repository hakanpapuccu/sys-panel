<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVacationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->hasPermission('create_vacations');
    }

    public function rules(): array
    {
        return [
            'vacation_date' => ['required', 'date', 'after_or_equal:today'],
            'vacation_why' => ['required', 'string', 'max:255'],
            'vacation_start' => ['required', 'date_format:H:i'],
            'vacation_end' => ['required', 'date_format:H:i', 'after:vacation_start'],
        ];
    }
}
