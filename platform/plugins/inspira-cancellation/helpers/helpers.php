<?php

use Botble\InspiraCancellation\Facades\InspiraCancellation;
use Illuminate\Database\Eloquent\Model;

if (! function_exists('inspira_cancellation_quote')) {
    function inspira_cancellation_quote(string $type, Model $booking): array
    {
        return InspiraCancellation::getCancellationQuote($type, $booking);
    }
}
