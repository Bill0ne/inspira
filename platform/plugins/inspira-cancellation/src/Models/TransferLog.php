<?php

namespace Botble\InspiraCancellation\Models;

use Botble\ACL\Models\User;
use Botble\Base\Models\BaseModel;
use Botble\Hotel\Models\Customer;
use Botble\InspiraCancellation\Enums\TransferStatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferLog extends BaseModel
{
    protected $table = 'insp_transfer_logs';

    protected $fillable = [
        'booking_id',
        'booking_type',
        'old_customer_id',
        'new_customer_id',
        'status',
        'payload',
        'requested_by',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'payload' => 'array',
        'status' => TransferStatusEnum::class,
        'approved_at' => 'datetime',
    ];

    public function oldCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'old_customer_id')->withDefault();
    }

    public function newCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'new_customer_id')->withDefault();
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'requested_by')->withDefault();
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by')->withDefault();
    }
}
