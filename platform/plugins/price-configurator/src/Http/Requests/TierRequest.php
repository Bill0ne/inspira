<?php

namespace Botble\PriceConfigurator\Http\Requests;

use Botble\Base\Rules\OnOffRule;
use Botble\PriceConfigurator\Enums\PriceConfiguratorStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class TierRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'priority' => 'required|integer|min:0',
            'is_exclusive' => [new OnOffRule()],
            'starts_at' => 'nullable|date_format:Y-m-d H:i:s',
            'ends_at' => 'nullable|date_format:Y-m-d H:i:s|after_or_equal:starts_at',
            'notes' => 'nullable|string|max:500',
            'status' => ['required', Rule::in(PriceConfiguratorStatusEnum::values())],
        ];
    }
}
