<?php

namespace Botble\Courses\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Botble\Base\Casts\SafeContent;

class CourseBookingAddress extends BaseModel
{
    protected $table = 'course_booking_addresses';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'country',
        'state',
        'city',
        'address',
        'zip',
        'course_booking_id',
    ];

    protected $casts = [
        'first_name' => SafeContent::class,
        'last_name' => SafeContent::class,
        'phone' => SafeContent::class,
        'email' => SafeContent::class,
        'country' => SafeContent::class,
        'state' => SafeContent::class,
        'city' => SafeContent::class,
        'address' => SafeContent::class,
        'zip' => SafeContent::class,
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(CourseBooking::class)->withDefault();
    }

    protected function fullAddress(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) =>
                (($attributes['address'] ?? null) ? $attributes['address'] . ', ' : null)
                . (($attributes['city'] ?? null) ? $attributes['city'] . ', ' : null)
                . (($attributes['state'] ?? null) ? $attributes['state'] . ', ' : null)
                . (($attributes['country'] ?? null) ? $attributes['country'] . ', ' : null)
                . (($attributes['zip'] ?? null) ? $attributes['zip'] . ', ' : null)
        );
    }


    protected function fullName(): Attribute
    {
        return Attribute::get(fn () => sprintf('%s %s', $this->first_name, $this->last_name));
    }
}
