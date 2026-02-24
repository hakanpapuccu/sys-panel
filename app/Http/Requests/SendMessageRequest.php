<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->hasPermission('access_chat');
    }

    public function rules(): array
    {
        $isGeneral = $this->boolean('is_general');

        return [
            'receiver_id' => [
                Rule::requiredIf(! $isGeneral),
                'nullable',
                'integer',
                'exists:users,id',
                Rule::notIn([(int) $this->user()?->id]),
            ],
            'message' => ['required', 'string', 'max:5000'],
            'is_general' => ['nullable', 'boolean'],
        ];
    }
}
