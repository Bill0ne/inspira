<?php

namespace Botble\Hotel\Models;

use Botble\Base\Models\BaseModel;
use Botble\Hotel\Models\Customer;
use Botble\Payment\Models\Payment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerCardOrder extends BaseModel
{
    protected $table = 'customer_card_orders';

    protected $fillable = [
        'customer_id',
        'card_template_id',
        'assigned_card_id',
        'amount',
        'status',
        'transaction_id',
        'payment_id',
        'completed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(CustomerCard::class, 'card_template_id');
    }

    public function assignedCard(): BelongsTo
    {
        return $this->belongsTo(CustomerCard::class, 'assigned_card_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}
