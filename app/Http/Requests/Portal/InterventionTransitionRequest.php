<?php

namespace App\Http\Requests\Portal;

use Illuminate\Foundation\Http\FormRequest;

class InterventionTransitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'status_id' => ['required', 'integer', 'min:1'],
            'message' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
