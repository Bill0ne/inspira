<?php

namespace Botble\Hotel\Services;

use Botble\Hotel\DTO\CardEffectDTO;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Hotel\Enums\CustomerCardCoverageType;
use Botble\Hotel\Models\CustomerCard;
use Botble\Hotel\Models\CustomerCardUsage;
use Botble\Payment\Enums\PaymentMethodEnum;
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
        if (! $booking->exists) {
            Log::warning('[CardFinalize] Booking missing or not persisted, aborting', [
                'booking_id' => $booking->getKey(),
            ]);

            return;
        }

        if (! $booking->customer_card_id) {
            Log::info('[CardFinalize] Booking has no customer card, skipping', [
                'booking_id' => $booking->getKey(),
            ]);

            return;
        }

        if ($booking->customer_card_units_used <= 0) {
            $booking->forceFill(['customer_card_units_used' => 1])->save();
        }

        DB::transaction(function () use ($booking) {
            $booking->refresh();

            if (! $booking->exists || ! $booking->customer_card_id) {
                Log::warning('[CardFinalize] Booking missing identifiers, aborting', [
                    'booking_id' => $booking->getKey(),
                    'card_id' => $booking->customer_card_id,
                ]);

                return;
            }

            $card = CustomerCard::query()->lockForUpdate()->find($booking->customer_card_id);

            Log::info('[CardFinalize] booking=' . $booking->getKey(), [
                'card_id' => $booking->customer_card_id,
                'units_used' => $booking->customer_card_units_used,
                'units_before' => $card?->units_remaining,
                'status' => (string) $booking->status,
                'payment_method' => (string) ($booking->payment_method ?? PaymentMethodEnum::CUSTOMER_CARD()),
            ]);

            if (! $card) {
                Log::warning('[CardFinalize] Card not found for booking ' . $booking->getKey());

                return;
            }

            if ($booking->customer_id && $card->assigned_to && $card->assigned_to !== $booking->customer_id) {
                Log::warning('[CardFinalize] Card ' . $card->getKey() . ' not assigned to booking ' . $booking->getKey());

                return;
            }

            $usageExists = CustomerCardUsage::query()
                ->where('booking_id', $booking->getKey())
                ->exists();

            if ($usageExists) {
                Log::info('[CardFinalize] Usage already recorded for booking ' . $booking->getKey());

                return;
            }

            $availableUnits = max(0, (int) $card->units_remaining);

            if ($availableUnits <= 0) {
                Log::warning('[CardFinalize] Card ' . $card->getKey() . ' has no remaining units for booking '
                    . $booking->getKey());

                return;
            }

            $unitsRequested = max(1, (int) $booking->customer_card_units_used);
            $units = max(1, min($unitsRequested, $availableUnits));
            $remainingUnits = max($availableUnits - $units, 0);

            Log::info('[CardFinalize] before units=' . $availableUnits . ', after units=' . $remainingUnits, [
                'booking_id' => $booking->getKey(),
            ]);

            $card->forceFill([
                'units_remaining' => $remainingUnits,
                'is_active' => $remainingUnits > 0 ? $card->is_active : false,
            ])->save();

            $coverage = $booking->customer_card_coverage_type;
            $coverageValue = $coverage instanceof CustomerCardCoverageType
                ? (string) $coverage->value
                : (string) ($coverage ?: CustomerCardCoverageType::PARTIAL->value);

            $booking->forceFill([
                'customer_card_units_used' => $units,
                'customer_card_coverage_type' => $coverageValue,
                'customer_card_discount' => $booking->customer_card_discount ?? $booking->customer_card_discount_gross ?? 0,
                'customer_card_discount_gross' => $booking->customer_card_discount_gross ?? $booking->customer_card_discount ?? 0,
                'customer_card_consumed_at' => now(),
                'status' => BookingStatusEnum::PROCESSING,
                'payment_method' => (string) PaymentMethodEnum::CUSTOMER_CARD(),
            ])->save();

            $usage = CustomerCardUsage::query()->create([
                'card_id' => $card->getKey(),
                'booking_id' => $booking->getKey(),
                'course_id' => $booking->course_id,
                'units_used' => $units,
                'discount_amount' => (float) ($booking->customer_card_discount_gross ?? 0),
                'discount_gross' => (float) ($booking->customer_card_discount_gross ?? 0),
                'coverage_type' => $coverageValue,
                'status' => 'consumed',
                'consumed_at' => now(),
            ]);

            Log::info('[CardFinalize] usage created=' . ($usage?->getKey() ?: 'n/a'), [
                'booking_id' => $booking->getKey(),
            ]);

            Log::info('[CardFinalize] booking status=' . (string) $booking->status . ' payment_method=' . (string) $booking->payment_method, [
                'booking_id' => $booking->getKey(),
            ]);
        });
    }
}
