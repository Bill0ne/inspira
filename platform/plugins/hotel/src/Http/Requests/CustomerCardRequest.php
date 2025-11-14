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
        if ($this->filled('template_id')) {
            return [
                'template_id' => ['required', 'integer', 'exists:ht_customer_cards,id'],
                'assigned_to' => ['required', 'integer', 'exists:ht_customers,id'],
                'valid_until' => ['nullable', 'date_format:' . BaseHelper::getDateFormat(), 'after:today'],
                'is_active' => ['sometimes', 'boolean'],
            ];
        }

        return [
            'name' => ['required', 'string', 'max:191'],
            'type' => ['required', Rule::in(CustomerCardTypeEnum::values())],
            'base_price' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'units_total' => ['required', 'integer', 'min:1'],
            'valid_until' => ['nullable', 'date_format:' . BaseHelper::getDateFormat(), 'after:today'],
            'is_active' => ['sometimes', 'boolean'],
            'is_single_purchase' => ['sometimes', 'boolean'],
            'assigned_to' => ['nullable', 'integer', 'exists:ht_customers,id'],
        ];
    }
}
