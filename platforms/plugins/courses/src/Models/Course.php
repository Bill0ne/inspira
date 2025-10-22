<?php

namespace Botble\Courses\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Hotel\Models\Tax;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends BaseModel
{
    protected $table = 'courses';

    protected $fillable = [
        'name',
        'thumbnail',
        'description',
        'price',
        'duration',
        'start_date',
        'end_date',
        'instructor_id',
        'category_id',
        'status',
        'unlimited_seats',
        'number_of_seats',
        'is_featured',
        'is_recurring',
        'recurring_type',
        'recurring_interval',
        'recurring_until',
        'tax_id',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
        'description' => SafeContent::class,
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'recurring_until' => 'datetime',
        'recurring_interval' => 'integer',
        'is_recurring' => 'boolean',
        'unlimited_seats' => 'boolean',
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function getCourseTotalPrice(): float
    {
        $price = $this->price;

        return $price;
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CourseReview::class, 'course_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(CourseBooking::class, 'course_id');
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class, 'tax_id')->withDefault();
    }

    public function hasAvailableSeats(): bool
    {
        if ($this->unlimited_seats) {
            return true;
        }

        $bookedSeats = $this->bookings()->count();

        return $bookedSeats < $this->number_of_seats;
    }

    public function isRecurring(): bool
    {
        return (bool) $this->is_recurring;
    }

    public function generateRecurringDates(): array
    {
        if (! $this->isRecurring() || ! $this->recurring_until) {
            return [];
        }

        $dates = [];
        $current = $this->start_date->copy();

        while ($current <= $this->recurring_until) {
            $dates[] = $current->copy();

            switch ($this->recurring_type) {
                case 'daily':
                    $current->addDays($this->recurring_interval);
                    break;
                case 'weekly':
                    $current->addWeeks($this->recurring_interval);
                    break;
                case 'monthly':
                    $current->addMonths($this->recurring_interval);
                    break;
            }
        }

        return $dates;
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(CourseSession::class, 'course_id');
    }


}
