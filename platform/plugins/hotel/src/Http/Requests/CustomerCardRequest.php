<?php

namespace Botble\Hotel\Http\Requests;

use Botble\Base\Facades\BaseHelper;
use Botble\Hotel\Enums\CustomerCardTypeEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CustomerCardRequest extends Request
{
    protected function prepareForValidation(): void
    {
        $unitsTotal = $this->input('units_total');

        if (! $unitsTotal && in_array($this->input('type'), ['5er', '10er'], true)) {
            $unitsTotal = $this->input('type') === '5er' ? 5 : 10;
        }

        $this->merge([
            'units_total' => $unitsTotal,
        ]);
    }

    public function rules(): array
    {
        $validUntilRules = $this->validUntilRules();

        if ($this->filled('template_id')) {
            return [
                'template_id' => ['required', 'integer', 'exists:ht_customer_cards,id'],
                'assigned_to' => ['required', 'integer', 'exists:ht_customers,id'],
                'valid_until' => $validUntilRules,
                'units_remaining' => ['sometimes', 'integer', 'min:0'],
                'is_active' => ['sometimes', 'boolean'],
            ];
        }

        return [
            'name' => ['required', 'string', 'max:191'],
            'type' => ['required', Rule::in(CustomerCardTypeEnum::values())],
            'base_price' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'units_total' => ['required', 'integer', 'min:1'],
            'units_remaining' => ['sometimes', 'integer', 'min:0'],
            'valid_until' => $validUntilRules,
            'is_active' => ['sometimes', 'boolean'],
            'is_single_purchase' => ['sometimes', 'boolean'],
            'assigned_to' => ['nullable', 'integer', 'exists:ht_customers,id'],
        ];
    }

    protected function validUntilRules(): array
    {
        $rules = ['nullable', 'date_format:' . BaseHelper::getDateFormat()];

        if ($this->isMethod('post')) {
            $rules[] = 'after:today';
        }

        return $rules;
    }
}
