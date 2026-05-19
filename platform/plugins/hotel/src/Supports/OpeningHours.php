<?php

namespace Botble\Hotel\Supports;

class OpeningHours
{
    public static function startLabel(): string
    {
        return self::normalize((string) config('hotel.opening_hours.start', '10:00'));
    }

    public static function endLabel(): string
    {
        return self::normalize((string) config('hotel.opening_hours.end', '21:00'));
    }

    public static function startMinutes(): int
    {
        return self::toMinutes(self::startLabel());
    }

    public static function endMinutes(): int
    {
        return self::toMinutes(self::endLabel());
    }

    /**
     * @return list<string>  Stunden-Slot-Labels wie "10:00 - 11:00" für jede volle Stunde innerhalb der Öffnungszeit.
     */
    public static function hourlySlots(): array
    {
        $slots = [];
        $start = self::startMinutes();
        $end = self::endMinutes();

        for ($current = $start; $current + 60 <= $end; $current += 60) {
            $slots[] = sprintf(
                '%s - %s',
                self::minutesToLabel($current),
                self::minutesToLabel($current + 60)
            );
        }

        return $slots;
    }

    protected static function normalize(string $value): string
    {
        $value = trim($value);

        if (preg_match('/^(\d{1,2}):(\d{2})$/', $value, $matches)) {
            return sprintf('%02d:%02d', (int) $matches[1], (int) $matches[2]);
        }

        return $value;
    }

    protected static function toMinutes(string $hhmm): int
    {
        if (! preg_match('/^(\d{1,2}):(\d{2})$/', $hhmm, $matches)) {
            return 0;
        }

        return ((int) $matches[1]) * 60 + (int) $matches[2];
    }

    protected static function minutesToLabel(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }
}
