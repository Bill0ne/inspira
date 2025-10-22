<?php

namespace Botble\PriceConfigurator\Http\Requests;

use Botble\PriceConfigurator\Enums\PriceConfiguratorStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;
use Botble\PriceConfigurator\Enums\ScopeEnum;
use Botble\PriceConfigurator\Enums\CalculationTypeEnum;
use Botble\PriceConfigurator\Enums\RoundingModeEnum;

class RuleRequest extends Request
{
    public function rules(): array
    {
        return [
            'price_tier_id' => 'required|exists:pconf_tiers,id',
            'customer_category_id' => 'required|exists:pconf_customer_categories,id',
            'scope' => ['required', Rule::in(ScopeEnum::values())],
            'target_ids' => 'nullable|array',
            'target_ids.*' => 'nullable',
            'calculation_type' => ['required', Rule::in(CalculationTypeEnum::values())],
            'calculation_value' => 'required|numeric|min:0',
            'rounding_mode' => ['required', Rule::in(RoundingModeEnum::values())],
            'round_to' => 'nullable|numeric|min:0',
            'status' => ['required', Rule::in(PriceConfiguratorStatusEnum::values())],
        ];
    }
}
