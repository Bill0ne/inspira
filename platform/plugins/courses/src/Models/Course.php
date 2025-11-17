<?php

namespace Botble\Courses\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Hotel\Models\Customer;
use Botble\Hotel\Models\Tax;
use Carbon\Carbon;
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
        'room_id',
        'accept_customer_card',
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
        'accept_customer_card' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::retrieved(function (Course $course) {
            $course->syncAutomaticStatus();
        });

        static::saved(function (Course $course) {
            $course->syncAutomaticStatus();
        });
    }

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

    public function getPriceWithTax(?float $price = null): float
    {
        $price = (float) ($price ?? $this->price);

        if ($price <= 0) {
            return $price;
        }

        $taxPercentage = (float) ($this->tax->percentage ?? 0);

        if ($taxPercentage <= 0) {
            return $price;
        }

        return $price * (1 + $taxPercentage / 100);
    }

    public function getTaxAmount(float $price): float
    {
        if ($price <= 0) {
            return 0;
        }

        $taxPercentage = (float) ($this->tax->percentage ?? 0);

        if ($taxPercentage <= 0) {
            return 0;
        }

        return $price * $taxPercentage / 100;
    }

    public function resolvePricing(?Customer $customer = null): array
    {
        $basePrice = $this->getCourseTotalPrice();
        $calculatedPrice = $basePrice;

        if (function_exists('is_plugin_active') && is_plugin_active('price-configurator')) {
            $calculatedPrice = app(\Botble\PriceConfigurator\Services\PriceConfiguratorService::class)
                ->calculatePrice(
                    $basePrice,
                    \Botble\PriceConfigurator\Enums\TargetTypeEnum::COURSE,
                    $this->getKey(),
                    $customer
                );
        }

        $baseWithTax = $this->getPriceWithTax($basePrice);
        $calculatedWithTax = $this->getPriceWithTax($calculatedPrice);

        return [
            'base_net' => $basePrice,
            'base_gross' => $baseWithTax,
            'calculated_net' => $calculatedPrice,
            'calculated_gross' => $calculatedWithTax,
            'discount_net' => $basePrice - $calculatedPrice,
            'discount_gross' => $baseWithTax - $calculatedWithTax,
        ];
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

    public function hasUpcomingSessions(): bool
    {
        return $this->sessions()
            ->where('end_date', '>', Carbon::now())
            ->exists();
    }

    public function syncAutomaticStatus(): void
    {
        if (! $this->exists || ! $this->status instanceof BaseStatusEnum) {
            return;
        }

        if (! static::hasExpiredStatusSupport()) {
            return;
        }

        $hasUpcomingSessions = $this->hasUpcomingSessions();

        if (
            $this->status->equals(BaseStatusEnum::PUBLISHED())
            && $this->shouldAutomaticallyExpire($hasUpcomingSessions)
        ) {
            $this->forceFill(['status' => BaseStatusEnum::EXPIRED])->saveQuietly();
            $this->status = BaseStatusEnum::EXPIRED();

            return;
        }

        if ($this->status->equals(BaseStatusEnum::EXPIRED()) && $hasUpcomingSessions) {
            $this->forceFill(['status' => BaseStatusEnum::PUBLISHED])->saveQuietly();
            $this->status = BaseStatusEnum::PUBLISHED();
        }
    }

    public static function hasExpiredStatusSupport(): bool
    {
        return defined(BaseStatusEnum::class . '::EXPIRED');
    }

    protected function shouldAutomaticallyExpire(?bool $hasUpcomingSessions = null): bool
    {
        $hasUpcomingSessions ??= $this->hasUpcomingSessions();

        if ($hasUpcomingSessions) {
            return false;
        }

        $hadSessions = $this->sessions()->exists();

        if ($hadSessions) {
            return true;
        }

        if ($this->end_date instanceof Carbon) {
            return $this->end_date->lte(Carbon::now());
        }

        return false;
    }

    public function isPast(): bool
    {
        $sessions = $this->sessions;
        if ($sessions->isEmpty()) {
            return $this->end_date && $this->end_date < Carbon::now();
        }

        return $sessions->every(fn($session) => $session->start_date < Carbon::now());
    }

    public function room()
    {
        return $this->belongsTo(\Botble\Hotel\Models\Room::class, 'room_id')->withDefault();
    }

}
