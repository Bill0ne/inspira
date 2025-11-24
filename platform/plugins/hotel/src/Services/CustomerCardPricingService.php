<?php

namespace Botble\Hotel\Services;

use Botble\Hotel\DTO\CardEffectDTO;
use Botble\Hotel\Enums\CustomerCardCoverageType;
use Botble\Hotel\Models\CustomerCard;
use Botble\Hotel\Models\CustomerCardUsage;
use Botble\Courses\Models\Course;
use Botble\Courses\Models\CourseBooking;
use Illuminate\Support\Facades\DB;

class CustomerCardPricingService
{
    public function calculateCardEffect(CustomerCard $card, Course $course, float $baseAmountGross): CardEffectDTO
    {
        $baseAmountGross = max($baseAmountGross, 0);
        $availableUnits = max($card->units_remaining, 0);

        if ($availableUnits <= 0 || $baseAmountGross <= 0) {
            return new CardEffectDTO(0, 0, 0, CustomerCardCoverageType::NONE);
        }

        $unitValueGross = max((float) $card->base_price, 0);
        $unitValueGross = $unitValueGross > 0 ? $unitValueGross : $baseAmountGross;

        $estimatedUnits = (int) ceil($baseAmountGross / max($unitValueGross, 1));
        $unitsUsed = max(1, min($availableUnits, $estimatedUnits));
        $discountGross = min($unitsUsed * $unitValueGross, $baseAmountGross);
        $coverageType = $discountGross >= $baseAmountGross
            ? CustomerCardCoverageType::FULL
            : CustomerCardCoverageType::PARTIAL;

        return new CardEffectDTO($discountGross, $unitsUsed, $unitValueGross, $coverageType);
    }

    public function finalizeUsage(CourseBooking $booking): void
    {
        if (! $booking->customer_card_id || $booking->customer_card_units_used <= 0) {
            return;
        }

        DB::transaction(function () use ($booking) {
            $card = CustomerCard::query()->lockForUpdate()->find($booking->customer_card_id);

            if (! $card) {
                return;
            }

            $usageExists = CustomerCardUsage::query()
                ->where('booking_id', $booking->getKey())
                ->exists();

            if ($usageExists) {
                return;
            }

            $units = max(1, min($booking->customer_card_units_used, $card->units_remaining));
            $card->decrement('units_remaining', $units);

            if ($card->units_remaining <= 0) {
                $card->update(['units_remaining' => 0, 'is_active' => false]);
            }

            $coverage = $booking->customer_card_coverage_type;

            if (is_string($coverage)) {
                $coverage = CustomerCardCoverageType::tryFrom($coverage);
            }

            $coverage ??= CustomerCardCoverageType::PARTIAL;

            CustomerCardUsage::query()->create([
                'card_id' => $card->getKey(),
                'booking_id' => $booking->getKey(),
                'course_id' => $booking->course_id,
                'units_used' => $units,
                'discount_amount' => (float) $booking->customer_card_discount_gross,
                'discount_gross' => (float) $booking->customer_card_discount_gross,
                'coverage_type' => $coverage->value,
                'status' => 'consumed',
            ]);

            $booking->forceFill(['customer_card_consumed_at' => now()])->save();
        });
    }
}
