<?php

namespace Botble\InspiraCancellation\Http\Requests;

use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CancellationRuleRequest extends Request
{
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['course', 'room'])],
            'from_days' => ['nullable', 'integer', 'min:0'],
            'to_days' => ['nullable', 'integer', 'min:0'],
            'refund_percent' => ['required', 'integer', 'min:0', 'max:100'],
            'description' => ['nullable', 'string'],
            'active' => ['sometimes', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
