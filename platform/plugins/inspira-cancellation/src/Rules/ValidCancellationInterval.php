<?php

namespace Botble\InspiraCancellation\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;

class ValidCancellationInterval implements DataAwareRule, ValidationRule
{
    protected array $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $fromRaw = Arr::get($this->data, 'from_days');
        $toRaw = Arr::get($this->data, 'to_days');

        if (! $this->isNullableInteger($fromRaw)) {
            $fail(trans('plugins/inspira-cancellation::cancellation.rule.validation.from_integer'));

            return;
        }

        if (! $this->isNullableInteger($toRaw)) {
            $fail(trans('plugins/inspira-cancellation::cancellation.rule.validation.to_integer'));

            return;
        }

        $from = $this->normalizeBound($fromRaw);
        $to = $this->normalizeBound($toRaw);

        if (! is_null($from) && $from < 0) {
            $fail(trans('plugins/inspira-cancellation::cancellation.rule.validation.from_min'));

            return;
        }

        if (! is_null($to) && $to < 0) {
            $fail(trans('plugins/inspira-cancellation::cancellation.rule.validation.to_min'));

            return;
        }

        if ($from === null || $to === null) {
            return;
        }

        if ($from > $to) {
            $fail(trans('plugins/inspira-cancellation::cancellation.rule.validation.invalid_range'));
        }
    }

    protected function isNullableInteger(mixed $value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    protected function normalizeBound(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }
}
