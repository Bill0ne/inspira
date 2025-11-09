<?php

namespace Botble\Hotel\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerCardUsage extends BaseModel
{
    protected $table = 'ht_customer_card_usages';

    protected $fillable = [
        'card_id',
        'booking_id',
        'course_id',
        'units_used',
        'discount_amount',
    ];

    protected $casts = [
        'units_used' => 'int',
        'discount_amount' => 'decimal:2',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(CustomerCard::class, 'card_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
