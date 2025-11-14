<?php

namespace Botble\InspiraCancellation\Rules;

use Botble\InspiraCancellation\Models\CancellationRule;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;

class NoOverlappingCancellationRule implements DataAwareRule, ValidationRule
{
    protected array $data = [];

    public function __construct(protected ?int $exceptId = null)
    {
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $type = (string) $value;

        if ($type === '') {
            return;
        }

        $fromRaw = Arr::get($this->data, 'from_days');
        $toRaw = Arr::get($this->data, 'to_days');

        if ($this->hasInvalidBound($fromRaw) || $this->hasInvalidBound($toRaw)) {
            return;
        }

        $isActive = $this->resolveBoolean(Arr::get($this->data, 'active'));

        if ($isActive === false) {
            return;
        }

        $from = $this->normalizeBound($fromRaw);
        $to = $this->normalizeBound($toRaw);

        $overlappingRule = CancellationRule::query()
            ->where('type', $type)
            ->where('active', true)
            ->when($this->exceptId, fn ($query) => $query->whereKeyNot($this->exceptId))
            ->get()
            ->first(function (CancellationRule $rule) use ($from, $to) {
                return $this->intervalsOverlap($from, $to, $rule->from_days, $rule->to_days);
            });

        if ($overlappingRule) {
            $fail(trans('plugins/inspira-cancellation::cancellation.rule.validation.overlap', [
                'rule' => $overlappingRule->getKey(),
            ]));
        }
    }

    protected function hasInvalidBound(mixed $value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_INT) === false;
    }

    protected function resolveBoolean(mixed $value): ?bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        $normalized = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($normalized === null) {
            return null;
        }

        return $normalized;
    }

    protected function normalizeBound(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    protected function intervalsOverlap(?int $from, ?int $to, ?int $existingFrom, ?int $existingTo): bool
    {
        $from = $from ?? PHP_INT_MIN;
        $to = $to ?? PHP_INT_MAX;
        $existingFrom = $existingFrom ?? PHP_INT_MIN;
        $existingTo = $existingTo ?? PHP_INT_MAX;

        return $from <= $existingTo && $existingFrom <= $to;
    }
}
