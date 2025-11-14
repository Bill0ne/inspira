<?php

namespace Botble\InspiraCancellation\Events;

use Botble\Hotel\Models\Customer;
use Botble\InspiraCancellation\Models\TransferLog;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransferRejectedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Model $booking;

    public TransferLog $log;

    public ?Customer $customer;

    public function __construct(Model $booking, TransferLog $log, ?Customer $customer = null)
    {
        $this->booking = $booking;
        $this->log = $log;
        $this->customer = $customer;
    }
}
