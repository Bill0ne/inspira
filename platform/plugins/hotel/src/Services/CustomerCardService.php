<?php

namespace Botble\Hotel\Services;

use Botble\Hotel\Models\CustomerCard;
use Botble\Hotel\Models\CustomerCardUsage;
use Botble\Hotel\Models\Booking;
use Botble\Courses\Models\Course;
use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class CustomerCardService
{
    public function __construct(protected DatabaseManager $database)
    {
    }

    public function getActiveCardsByUser(?int $userId): Collection
    {
        return CustomerCard::query()
            ->when($userId, function ($query, $userId) {
                $query->where(function ($q) use ($userId) {
                    $q->whereNull('assigned_to')->orWhere('assigned_to', $userId);
                });
            })
            ->where('is_active', true)
            ->where('units_remaining', '>', 0)
            ->where(function ($query) {
                $query->whereNull('valid_until')->orWhere('valid_until', '>=', Carbon::now());
            })
            ->orderBy('name')
            ->get();
    }

    public function getApplicableCardsForCourse(?int $courseId, ?int $userId): Collection
    {
        $cards = $this->getActiveCardsByUser($userId);

        if (! $courseId || ! class_exists(Course::class)) {
            return $cards;
        }

        $course = Course::query()->find($courseId);

        if (! $course || ! Arr::get($course->toArray(), 'accept_customer_card')) {
            return collect();
        }

        return $cards;
    }

    public function calculateDiscount(CustomerCard $card, ?Course $course, int $units = 1): float
    {
        $discountPerUnit = (float) $card->base_price * ($card->discount_percent / 100);

        if ($course && $course->price) {
            $discountPerUnit = min($discountPerUnit, (float) $course->price);
        }

        return round($discountPerUnit * max($units, 1), 2);
    }

    public function consumeUnits(CustomerCard $card, ?Booking $booking, ?Course $course, int $units, float $discountAmount): CustomerCardUsage
    {
        $units = max(0, min($units, $card->units_remaining));

        return $this->database->transaction(function () use ($card, $booking, $course, $units, $discountAmount) {
            if ($units > 0) {
                $card->decrement('units_remaining', $units);
                $card->refresh();
            }

            if ($card->units_remaining <= 0) {
                $card->update(['is_active' => false, 'units_remaining' => 0]);
            }

            return CustomerCardUsage::query()->create([
                'card_id' => $card->getKey(),
                'booking_id' => $booking?->getKey(),
                'course_id' => $course?->getKey(),
                'units_used' => $units,
                'discount_amount' => $discountAmount,
            ]);
        });
    }
}
