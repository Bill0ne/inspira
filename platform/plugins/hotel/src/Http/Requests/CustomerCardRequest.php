<?php

namespace Botble\Hotel\Http\Requests;

use Botble\Base\Facades\BaseHelper;
use Botble\Hotel\Enums\CustomerCardTypeEnum;
use Botble\Hotel\Models\CustomerCard;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class CustomerCardRequest extends Request
{
    protected function prepareForValidation(): void
    {
        $slug = $this->input('slug');

        if (! $slug && $this->input('name')) {
            $slug = Str::slug($this->input('name'));
        }

        $this->merge([
            'slug' => $slug,
            'units_remaining' => $this->filled('units_remaining')
                ? $this->input('units_remaining')
                : $this->input('units_total'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => [
                'required',
                'string',
                'max:150',
                Rule::unique(CustomerCard::class, 'slug')->ignore($this->route('customer_card')),
            ],
            'type' => ['required', 'string', Rule::in(CustomerCardTypeEnum::values())],
            'units_total' => ['required', 'integer', 'min:1'],
            'units_remaining' => ['nullable', 'integer', 'min:0'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['required', 'integer', 'min:0', 'max:100'],
            'valid_until' => ['nullable', 'date_format:' . BaseHelper::getDateFormat()],
            'is_active' => ['sometimes', 'boolean'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
