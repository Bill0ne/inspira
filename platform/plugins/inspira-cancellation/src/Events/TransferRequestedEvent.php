<?php

namespace Botble\InspiraCancellation\Events;

use Botble\InspiraCancellation\Models\TransferLog;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransferRequestedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Model $booking;

    public TransferLog $log;

    public array $payload;

    public function __construct(Model $booking, TransferLog $log, array $payload = [])
    {
        $this->booking = $booking;
        $this->log = $log;
        $this->payload = $payload;
    }
}
