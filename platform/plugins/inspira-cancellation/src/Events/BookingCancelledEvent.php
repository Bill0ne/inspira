<?php

namespace Botble\InspiraCancellation\Events;

use Botble\InspiraCancellation\Models\Cancellation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCancelledEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Model $booking;

    public Cancellation $cancellation;

    public array $quote;

    public function __construct(Model $booking, Cancellation $cancellation, array $quote)
    {
        $this->booking = $booking;
        $this->cancellation = $cancellation;
        $this->quote = $quote;
    }
}
