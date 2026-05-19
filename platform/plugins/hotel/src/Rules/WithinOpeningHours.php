<?php

namespace Botble\Hotel\Rules;

use Botble\Hotel\Supports\OpeningHours;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Throwable;

class WithinOpeningHours implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        [$startMinutes, $endMinutes] = $this->extractMinutes($value);

        if ($startMinutes === null || $endMinutes === null) {
            return;
        }

        $openingStart = OpeningHours::startMinutes();
        $openingEnd = OpeningHours::endMinutes();

        if ($startMinutes < $openingStart || $endMinutes > $openingEnd) {
            $fail(trans('plugins/hotel::booking.opening_hours_violation', [
                'start' => OpeningHours::startLabel(),
                'end' => OpeningHours::endLabel(),
            ]));
        }
    }

    /**
     * @return array{0: ?int, 1: ?int}
     */
    protected function extractMinutes(mixed $value): array
    {
        if (is_string($value)) {
            if (preg_match('/^\d{2}\.\d{2}\.\d{4}\s+(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})$/', trim($value), $matches)) {
                return [$this->toMinutes($matches[1]), $this->toMinutes($matches[2])];
            }

            $carbon = $this->safeParse($value);

            if ($carbon) {
                $minutes = $carbon->hour * 60 + $carbon->minute;

                return [$minutes, $minutes];
            }

            return [null, null];
        }

        if (is_array($value)) {
            $start = $value['start_date'] ?? $value['start'] ?? null;
            $end = $value['end_date'] ?? $value['end'] ?? null;

            $startMinutes = $this->dateValueToMinutes($start);
            $endMinutes = $this->dateValueToMinutes($end);

            return [$startMinutes, $endMinutes];
        }

        return [null, null];
    }

    protected function dateValueToMinutes(mixed $value): ?int
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        if (preg_match('/^\d{1,2}:\d{2}$/', trim($value))) {
            return $this->toMinutes(trim($value));
        }

        $carbon = $this->safeParse($value);

        return $carbon ? $carbon->hour * 60 + $carbon->minute : null;
    }

    protected function safeParse(string $value): ?Carbon
    {
        foreach (['d.m.Y H:i', 'Y-m-d H:i', 'd.m.Y H:i:s', 'Y-m-d H:i:s'] as $format) {
            try {
                return Carbon::createFromFormat($format, trim($value));
            } catch (Throwable) {
                continue;
            }
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }

    protected function toMinutes(string $hhmm): int
    {
        [$h, $m] = explode(':', $hhmm);

        return ((int) $h) * 60 + (int) $m;
    }
}
