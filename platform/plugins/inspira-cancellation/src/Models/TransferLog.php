<?php

namespace Botble\InspiraCancellation\Models;

use Botble\Base\Models\BaseModel;
use Botble\Hotel\Models\Customer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferLog extends BaseModel
{
    protected $table = 'insp_transfer_logs';

    protected $fillable = [
        'booking_id',
        'booking_type',
        'old_customer_id',
        'new_customer_id',
    ];

    public function oldCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'old_customer_id')->withDefault();
    }

    public function newCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'new_customer_id')->withDefault();
    }
}
