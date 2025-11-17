<?php

namespace Botble\InspiraCancellation\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array getCancellationQuote(string $type, \Illuminate\Database\Eloquent\Model $booking)
 * @method static \Botble\InspiraCancellation\Models\Cancellation createCancellation(\Illuminate\Database\Eloquent\Model $booking, array $quote, array $context = [])
 * @method static \Botble\InspiraCancellation\Models\CancellationRule|null findRuleFor(string $type, \Carbon\CarbonInterface $startDate)
 * @method static \Illuminate\Database\Eloquent\Model|null markBookingAsCancelled(\Botble\InspiraCancellation\Models\Cancellation $cancellation)
 */
class InspiraCancellation extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'inspira.cancellation';
    }
}
