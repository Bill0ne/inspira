<?php

namespace Botble\InspiraCancellation\Models;

use Botble\ACL\Models\User;
use Botble\Base\Models\BaseModel;
use Botble\Hotel\Models\Customer;
use Botble\InspiraCancellation\Enums\CancellationStatusEnum;
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
        'approved_at',
        'approved_by',
        'refunded_at',
        'refunded_by',
    ];

    protected $casts = [
        'refund_amount' => 'float',
        'refund_percent' => 'int',
        'status' => CancellationStatusEnum::class,
        'approved_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(CancellationRule::class, 'rule_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id')->withDefault();
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by')->withDefault();
    }

    public function refunder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'refunded_by')->withDefault();
    }
}
