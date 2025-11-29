<?php

namespace Botble\Hotel\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Botble\Hotel\Models\CustomerCard;
use Botble\Hotel\Models\Booking;
use Botble\Courses\Models\Course;
use Botble\Courses\Models\CourseBooking;

class CustomerCardUsage extends BaseModel
{
    protected $table = 'ht_customer_card_usages';

    protected $fillable = [
        'card_id',
        'booking_id',
        'course_booking_id',
        'course_id',
        'units_used',
        'discount_amount',
        'discount_gross',
        'coverage_type',
        'status',
        'consumed_at',
    ];

    protected $casts = [
        'units_used' => 'int',
        'discount_amount' => 'decimal:2',
        'discount_gross' => 'decimal:2',
        'coverage_type' => 'string',
        'consumed_at' => 'datetime',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(CustomerCard::class, 'card_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function courseBooking(): BelongsTo
    {
        return $this->belongsTo(CourseBooking::class, 'course_booking_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
