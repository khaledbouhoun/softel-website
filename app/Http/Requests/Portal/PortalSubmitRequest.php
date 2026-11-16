<?php

namespace App\Http\Requests\Portal;

use Illuminate\Foundation\Http\FormRequest;

class PortalSubmitRequest extends FormRequest
{
    public const PHONE_REGEX = '/^\+?[0-9][0-9 .\-()\/]{4,28}$/';

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
            'type' => ['required', 'in:demo,quotation,trial'],
            'product_id' => ['required', 'integer', 'min:1'],
            'company' => ['required', 'string', 'max:150'],
            'contact_name' => ['required', 'string', 'max:150'],
            'job_title' => ['nullable', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30', 'regex:'.self::PHONE_REGEX],
            'email' => ['required', 'string', 'email:rfc', 'max:191'],
            'wilaya' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'business_activity' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'slot_start' => ['nullable', 'required_if:type,demo', 'date'],
        ];
    }
}
