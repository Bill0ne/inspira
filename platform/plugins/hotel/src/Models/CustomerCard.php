<?php

namespace Botble\Hotel\Models;

use Botble\ACL\Models\User;
use Botble\Base\Models\BaseModel;
use Botble\Hotel\Enums\CustomerCardTypeEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerCard extends BaseModel
{
    protected $table = 'ht_customer_cards';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'units_total',
        'units_remaining',
        'base_price',
        'discount_percent',
        'valid_until',
        'is_active',
        'created_by',
        'assigned_to',
    ];

    protected $casts = [
        'type' => CustomerCardTypeEnum::class,
        'units_total' => 'int',
        'units_remaining' => 'int',
        'base_price' => 'decimal:2',
        'discount_percent' => 'int',
        'valid_until' => 'datetime',
        'is_active' => 'bool',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CustomerCardUsage::class, 'card_id');
    }
}
