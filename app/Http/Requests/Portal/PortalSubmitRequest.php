<?php

namespace App\Http\Requests\Portal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            // One product (product_id, kept for product pages and old links) or the checklist (product_ids).
            'product_id' => ['required_without:product_ids', 'prohibits:product_ids', 'integer', 'min:1'],
            'product_ids' => ['required_without:product_id', 'array', 'min:1', 'max:20'],
            'product_ids.*' => ['integer', 'min:1', 'distinct'],
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

    public function messages(): array
    {
        return [
            'product_ids.required_without' => __('site.portal_select_one_product'),
            'product_id.required_without' => __('site.portal_select_one_product'),
        ];
    }

    /** A presentation holds one time slot, so it is booked for a single product. */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if ($this->input('type') === 'demo' && count((array) $this->input('product_ids', [])) > 1) {
                $v->errors()->add('product_ids', __('site.portal_presentation_single'));
            }
        });
    }

    /** @return list<int> the chosen product ids, whichever field carried them */
    public function productIds(): array
    {
        $ids = $this->validated('product_ids') ?? [$this->validated('product_id')];

        return array_values(array_map('intval', array_filter((array) $ids)));
    }
}
