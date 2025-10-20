<?php

namespace Botble\Courses\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        if ($this->available_seats === null) {
            return true;
        }

        $bookedSeats = $this->bookings()->count();

        return $bookedSeats < $this->available_seats;
    }
}

