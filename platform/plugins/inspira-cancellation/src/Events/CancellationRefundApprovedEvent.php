<?php

namespace Botble\InspiraCancellation\Events;

use Botble\InspiraCancellation\Models\Cancellation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CancellationRefundApprovedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Cancellation $cancellation;

    public function __construct(Cancellation $cancellation)
    {
        $this->cancellation = $cancellation;
    }
}
