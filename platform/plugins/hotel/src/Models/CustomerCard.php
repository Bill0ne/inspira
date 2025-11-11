<?php

namespace Botble\Hotel\Models;

use Botble\ACL\Models\User;
use Botble\Base\Models\BaseModel;
use Botble\Hotel\Enums\CustomerCardTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Botble\Hotel\Models\Customer;
use Botble\Hotel\Models\CustomerCardUsage;
use Illuminate\Support\Str;

class CustomerCard extends BaseModel
{
    protected $table = 'ht_customer_cards';

    protected $fillable = [
        'name',
        'uid',
        'type',
        'base_price',
        'discount_percent',
        'units_total',
        'units_remaining',
        'valid_until',
        'is_active',
        'created_by',
        'assigned_to',
    ];

    protected $casts = [
        'type' => CustomerCardTypeEnum::class,
        'base_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'units_total' => 'int',
        'units_remaining' => 'int',
        'valid_until' => 'datetime',
        'is_active' => 'bool',
    ];

    protected $appends = ['status_label', 'status_color'];

    protected static function booted(): void
    {
        static::creating(function (CustomerCard $card): void {
            if ($card->assigned_to && ! $card->uid) {
                $card->uid = static::generateUid();
            }
        });

        static::saving(function (CustomerCard $card): void {
            if ($card->assigned_to && ! $card->uid) {
                $card->uid = static::generateUid();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'assigned_to');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CustomerCardUsage::class, 'card_id');
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->valid_until && $this->valid_until->isPast()) {
                return trans('plugins/hotel::customer-card.status.expired');
            }

            if (! $this->is_active || $this->units_remaining <= 0) {
                return trans('plugins/hotel::customer-card.status.consumed');
            }

            if ($this->valid_until && $this->valid_until->diffInDays(Carbon::now()) <= 7) {
                return trans('plugins/hotel::customer-card.status.warning');
            }

            return trans('plugins/hotel::customer-card.status.active');
        });
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->valid_until && $this->valid_until->isPast()) {
            return 'danger';
        }

        if (! $this->is_active || $this->units_remaining <= 0) {
            return 'danger';
        }

        if ($this->valid_until && $this->valid_until->diffInDays(Carbon::now()) <= 7) {
            return 'warning';
        }

        return 'success';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('units_remaining', '>', 0)
            ->where(function ($q) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', Carbon::now());
            });
    }

    public static function generateUid(): string
    {
        do {
            $uid = strtoupper(Str::random(12));
        } while (static::query()->where('uid', $uid)->exists());

        return $uid;
    }
}
