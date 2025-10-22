<?php

namespace Botble\PriceConfigurator\Http\Requests;

use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;
use Botble\PriceConfigurator\Enums\PriceConfiguratorStatusEnum;

class CustomerCategoryRequest extends Request
{
    public function rules(): array
    {
        $categoryId = $this->route('customer_category') ?? $this->route('id');

        return [
            'code' => [
                'required',
                'string',
                'max:120',
                Rule::unique('pconf_customer_categories', 'code')->ignore($categoryId),
            ],
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(PriceConfiguratorStatusEnum::values())],
        ];
    }
}
