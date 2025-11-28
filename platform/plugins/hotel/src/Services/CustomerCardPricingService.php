<?php

namespace Botble\Hotel\Services;

use Botble\Hotel\DTO\CardEffectDTO;
use Botble\Hotel\Enums\CustomerCardCoverageType;
use Botble\Hotel\Models\CustomerCard;
use Botble\Hotel\Models\CustomerCardUsage;
use Botble\Courses\Models\Course;
use Botble\Courses\Models\CourseBooking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        if (! $booking->customer_card_id) {
            Log::info('[CustomerCardDebug] finalizeUsage skipped: booking without card', [
                'booking_id' => $booking->getKey(),
            ]);

            return;
        }

        $unitsUsed = max((int) $booking->customer_card_units_used, 1);

        if ($booking->customer_card_units_used !== $unitsUsed) {
            $booking->forceFill(['customer_card_units_used' => $unitsUsed])->save();
        }

        DB::transaction(function () use ($booking, $unitsUsed) {
            $card = CustomerCard::query()->lockForUpdate()->find($booking->customer_card_id);

            if (! $card) {
                Log::warning('[CustomerCardDebug] finalizeUsage guard: card not found', [
                    'booking_id' => $booking->getKey(),
                    'card_id' => $booking->customer_card_id,
                ]);

                return;
            }

            if ($booking->customer_id && $card->assigned_to && $card->assigned_to !== $booking->customer_id) {
                Log::warning('[CustomerCardDebug] finalizeUsage guard: card not assigned to booking customer', [
                    'booking_id' => $booking->getKey(),
                    'card_id' => $card->getKey(),
                    'assigned_to' => $card->assigned_to,
                    'booking_customer_id' => $booking->customer_id,
                ]);

                return;
            }

            $usageExists = CustomerCardUsage::query()
                ->where('booking_id', $booking->getKey())
                ->exists();

            if ($usageExists) {
                Log::info('[CustomerCardDebug] finalizeUsage guard: usage already exists', [
                    'booking_id' => $booking->getKey(),
                ]);

                return;
            }

            $availableUnits = max(0, (int) $card->units_remaining);
            $unitsToUse = max(1, $unitsUsed);

            if ($availableUnits < $unitsToUse) {
                Log::warning('[CustomerCardDebug] finalizeUsage warning: insufficient units', [
                    'booking_id' => $booking->getKey(),
                    'card_id' => $card->getKey(),
                    'units_requested' => $unitsToUse,
                    'units_available' => $availableUnits,
                ]);
            }

            $remainingUnits = max($availableUnits - $unitsToUse, 0);

            Log::info('[CustomerCardDebug] finalizeUsage applying consumption', [
                'card_id' => $card->getKey(),
                'booking_id' => $booking->getKey(),
                'units_before' => $availableUnits,
                'units_to_use' => $unitsToUse,
                'units_after' => $remainingUnits,
            ]);

            $card->forceFill([
                'units_remaining' => $remainingUnits,
                'is_active' => $remainingUnits > 0 ? $card->is_active : false,
            ])->save();

            $coverage = $booking->customer_card_coverage_type;
            $coverageValue = $coverage instanceof CustomerCardCoverageType
                ? $coverage->value
                : ($coverage ?: CustomerCardCoverageType::PARTIAL->value);

            CustomerCardUsage::query()->create([
                'card_id' => $card->getKey(),
                'booking_id' => $booking->getKey(),
                'course_id' => $booking->course_id,
                'units_used' => $unitsToUse,
                'discount_amount' => (float) $booking->customer_card_discount,
                'discount_gross' => (float) $booking->customer_card_discount_gross,
                'coverage_type' => $coverageValue,
                'status' => 'consumed',
                'consumed_at' => now(),
            ]);

            $booking->forceFill(['customer_card_consumed_at' => now()])->save();

            Log::info('[CustomerCardDebug] finalizeUsage recorded usage', [
                'booking_id' => $booking->getKey(),
                'card_id' => $card->getKey(),
                'units_used' => $unitsToUse,
                'units_after' => $remainingUnits,
            ]);
        });
    }
}
