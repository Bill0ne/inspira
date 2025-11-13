<?php

namespace Botble\InspiraCancellation\Events;

use Botble\Hotel\Models\Customer;
use Botble\InspiraCancellation\Models\TransferLog;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingTransferredEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Model $booking;

    public TransferLog $log;

    public Customer $newCustomer;

    public ?int $oldCustomerId;

    public ?string $oldCustomerEmail;

    public function __construct(Model $booking, Customer $newCustomer, ?int $oldCustomerId, TransferLog $log, ?string $oldCustomerEmail = null)
    {
        $this->booking = $booking;
        $this->newCustomer = $newCustomer;
        $this->oldCustomerId = $oldCustomerId;
        $this->log = $log;
        $this->oldCustomerEmail = $oldCustomerEmail;
    }
}
