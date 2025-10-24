<?php

namespace Botble\Courses\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Botble\Hotel\Enums\BookingStatusEnum;

class CourseSession extends BaseModel
{
    protected $table = 'course_sessions';

    protected $fillable = [
        'course_id',
        'start_date',
        'end_date',
        'is_manual',
        'available_seats',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(CourseBooking::class, 'course_session_id');
    }

    public function hasAvailableSeats(): bool
    {
        $bookedCount = $this->bookings()
            ->whereIn('status', [
                BookingStatusEnum::PENDING,
                BookingStatusEnum::PROCESSING,
                BookingStatusEnum::COMPLETED,
            ])
            ->count();

        return $bookedCount < $this->available_seats;
    }

    public function getBookedCount(): int
    {
        return $this->bookings()
            ->whereIn('status', [
                BookingStatusEnum::PENDING,
                BookingStatusEnum::PROCESSING,
                BookingStatusEnum::COMPLETED,
            ])
            ->count();
    }

    public function activeBookings(): HasMany
    {
        return $this->hasMany(CourseBooking::class, 'course_session_id')
            ->whereIn('status', [
                BookingStatusEnum::PENDING,
               BookingStatusEnum::PROCESSING,
                BookingStatusEnum::COMPLETED,
            ]);
    }
}

