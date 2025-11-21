<?php

namespace Botble\PriceConfigurator\Http\Requests;

use Botble\Base\Rules\OnOffRule;
use Botble\PriceConfigurator\Enums\PriceConfiguratorStatusEnum;
use Botble\Support\Http\Requests\Request;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class TierRequest extends Request
{
    protected function prepareForValidation(): void
    {
        $formattedDates = [];

        foreach (['starts_at', 'ends_at'] as $field) {
            $value = $this->input($field);

            if (blank($value)) {
                continue;
            }

            $parsed = $this->parseDate($value);

            if ($parsed) {
                $formattedDates[$field] = $parsed->format('Y-m-d H:i:s');
            }
        }

        if ($formattedDates !== []) {
            $this->merge($formattedDates);
        }
    }

    protected function parseDate(string $value): ?Carbon
    {
        $normalized = trim($value);

        foreach (['Y-m-d H:i:s', 'Y-m-d H:i', 'd.m.Y H:i', 'd.m.Y H:i:s'] as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $normalized);
            } catch (\Throwable) {
                $parsed = false;
            }

            if ($parsed !== false && $parsed->format($format) === $normalized) {
                return $parsed;
            }
        }

        try {
            return Carbon::parse($normalized);
        } catch (\Throwable) {
            return null;
        }
    }

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
