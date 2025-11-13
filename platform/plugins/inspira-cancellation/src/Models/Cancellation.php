<?php

namespace Botble\InspiraCancellation\Models;

use Botble\Base\Models\BaseModel;
use Botble\Hotel\Models\Customer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cancellation extends BaseModel
{
    protected $table = 'insp_cancellations';

    protected $fillable = [
        'booking_id',
        'booking_type',
        'booking_reference',
        'customer_id',
        'rule_id',
        'refund_amount',
        'refund_percent',
        'status',
        'notes',
    ];

    protected $casts = [
        'refund_amount' => 'float',
        'refund_percent' => 'int',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(CancellationRule::class, 'rule_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id')->withDefault();
    }
}
