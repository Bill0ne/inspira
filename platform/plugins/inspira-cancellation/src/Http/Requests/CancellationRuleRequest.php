<?php

namespace Botble\InspiraCancellation\Http\Requests;

use Botble\InspiraCancellation\Rules\NoOverlappingCancellationRule;
use Botble\InspiraCancellation\Rules\ValidCancellationInterval;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CancellationRuleRequest extends Request
{
    public function rules(): array
    {
        $currentRuleId = $this->route('rule')?->getKey();

        return [
            'type' => [
                'required',
                Rule::in(['course', 'room']),
                new ValidCancellationInterval(),
                new NoOverlappingCancellationRule($currentRuleId),
            ],
            'from_days' => ['nullable'],
            'to_days' => ['nullable'],
            'refund_percent' => ['required', 'integer', 'min:0', 'max:100'],
            'description' => ['nullable', 'string'],
            'active' => ['sometimes', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
