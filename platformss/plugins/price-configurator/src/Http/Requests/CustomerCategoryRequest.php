<?php

namespace Botble\PriceConfigurator\Http\Requests;

use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;
use Botble\PriceConfigurator\Enums\PriceConfiguratorStatusEnum;

class CustomerCategoryRequest extends Request
{
    public function rules(): array
    {
        return [
            'code' => 'required|string|max:120|unique:pconf_customer_categories,code,' . $this->route('id'),
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(PriceConfiguratorStatusEnum::values())],
        ];
    }
}
