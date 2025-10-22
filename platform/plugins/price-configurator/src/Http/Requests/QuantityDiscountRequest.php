<?php

namespace Botble\PriceConfigurator\Http\Requests;

use Botble\PriceConfigurator\Enums\ConditionTypeEnum;
use Botble\PriceConfigurator\Enums\CalculationTypeEnum;
use Botble\PriceConfigurator\Enums\PriceConfiguratorStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class QuantityDiscountRequest extends Request
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'condition_type' => ['required', Rule::in(ConditionTypeEnum::values())],
            'range_min' => 'nullable|numeric|min:0',
            'range_max' => 'nullable|numeric|min:0|gte:range_min',
            'discount_type' => ['required', Rule::in(CalculationTypeEnum::values())],
            'discount_value' => 'required|numeric|min:0',
            'priority' => 'nullable|integer|min:0',
            'status' => ['required', Rule::in(PriceConfiguratorStatusEnum::values())],
        ];
    }
}
