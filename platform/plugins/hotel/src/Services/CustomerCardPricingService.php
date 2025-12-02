<?php

namespace Botble\Hotel\Services;

use Botble\Hotel\DTO\CardEffectDTO;
use Botble\Hotel\Enums\CustomerCardCoverageType;
use Botble\Hotel\Models\CustomerCard;
use Botble\Hotel\Models\CustomerCardUsage;
use Botble\Courses\Models\Course;
use Botble\Courses\Models\CourseBooking;
use Botble\Payment\Enums\PaymentMethodEnum;
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

        $basePrice = $card->base_price;
        $unitValueGross = ($basePrice !== null && $basePrice !== '')
            ? max((float) $basePrice, 0)
            : max($baseAmountGross, 0);

        // Jede Buchung soll genau einen Karteneinsatz verbrauchen.
        $unitsUsed = min($availableUnits, 1);

        // Der abzuziehende Betrag entspricht dem Einheitswert, höchstens jedoch dem Basisbetrag.
        $discountGross = min($unitValueGross, $baseAmountGross);
        $coverageType = $discountGross >= $baseAmountGross
            ? CustomerCardCoverageType::FULL
            : CustomerCardCoverageType::PARTIAL;

        return new CardEffectDTO($discountGross, $unitsUsed, $unitValueGross, $coverageType);
    }

    public function finalizeUsage(CourseBooking $booking): void
    {
        if (! $booking->customer_card_id) {
            return;
        }

        DB::transaction(function () use ($booking) {
            $card = CustomerCard::query()->lockForUpdate()->find($booking->customer_card_id);

            Log::info('[CustomerCardFinalize] Start booking ' . $booking->getKey(), [
                'card_id' => $booking->customer_card_id,
                'units_used' => $booking->customer_card_units_used,
                'units_before' => $card?->units_remaining,
            ]);

            if (! $card) {
                Log::warning('[CustomerCardFinalize] Card not found for booking ' . $booking->getKey());

                return;
            }

            if ($booking->customer_id && $card->assigned_to && $card->assigned_to !== $booking->customer_id) {
                Log::warning('[CustomerCardFinalize] Card ' . $card->getKey() . ' not assigned to booking ' . $booking->getKey());

                return;
            }

            $usageExists = CustomerCardUsage::query()
                ->where('course_booking_id', $booking->getKey())
                ->where(function ($query) {
                    $query
                        ->whereNull('status')
                        ->orWhere('status', '!=', 'reversed');
                })
                ->exists();

            if ($usageExists) {
                Log::info('[CustomerCardFinalize] Usage already recorded for booking ' . $booking->getKey());

                if (! $booking->customer_card_consumed_at) {
                    $booking->forceFill(['customer_card_consumed_at' => now()])->save();
                }

                return;
            }

            $unitsRequested = max(0, (int) $booking->customer_card_units_used);

            if ($unitsRequested <= 0) {
                Log::warning('[CustomerCardFinalize] Booking ' . $booking->getKey() . ' requested invalid units');

                return;
            }

            $availableUnits = max(0, (int) $card->units_remaining);
            if ($availableUnits <= 0) {
                Log::warning('[CustomerCardFinalize] Card ' . $card->getKey() . ' has no remaining units for booking '
                    . $booking->getKey());

                return;
            }

            if ($card->units_remaining < $unitsRequested) {
                Log::warning('[CustomerCardFinalize] Card ' . $card->getKey() . ' has '
                    . $card->units_remaining . ' units, booking requested ' . $unitsRequested . ' units');
            }

            $units = max(1, min($unitsRequested, $availableUnits));
            $remainingUnits = max($availableUnits - $units, 0);

            if ($booking->customer_card_units_used !== $units) {
                $booking->forceFill(['customer_card_units_used' => $units])->save();
            }

            Log::info('[CustomerCardFinalize] Units before: ' . $availableUnits . ' for booking ' . $booking->getKey());

            $card->forceFill([
                'units_remaining' => $remainingUnits,
                'is_active' => $remainingUnits > 0 ? $card->is_active : false,
            ])->save();

            Log::info('[CustomerCardFinalize] Units after: ' . $remainingUnits . ' for booking ' . $booking->getKey());

            $coverage = $booking->customer_card_coverage_type;
            $coverageValue = $coverage instanceof CustomerCardCoverageType
                ? $coverage->value
                : ($coverage ?: CustomerCardCoverageType::PARTIAL->value);

            CustomerCardUsage::query()->create([
                'card_id' => $card->getKey(),
                'course_booking_id' => $booking->getKey(),
                'course_id' => $booking->course_id,
                'units_used' => $units,
                'discount_amount' => (float) $booking->customer_card_discount_gross,
                'discount_gross' => (float) $booking->customer_card_discount_gross,
                'coverage_type' => $coverageValue,
                'status' => 'consumed',
                'consumed_at' => now(),
            ]);

            $booking->forceFill(['customer_card_consumed_at' => now()])->save();

            Log::info('[CustomerCardFinalize] Recorded usage for booking ' . $booking->getKey()
                . ' with card ' . $card->getKey() . ' using ' . $units . ' units');
        });
    }

    public function rebookUsage(
        CustomerCardUsage $usage,
        CustomerCard $targetCard,
        CourseBooking $booking,
        int $unitsUsed,
        float $discountGross,
        string $coverageType
    ): ?CustomerCardUsage {
        return DB::transaction(function () use (
            $usage,
            $targetCard,
            $booking,
            $unitsUsed,
            $discountGross,
            $coverageType
        ) {
            $sourceCard = CustomerCard::query()->lockForUpdate()->find($usage->card_id);

            $coverageValue = CustomerCardCoverageType::tryFrom($coverageType)?->value ?? $coverageType;

            if ($sourceCard) {
                $sourceCard->increment('units_remaining', max(0, (int) $usage->units_used));
                $sourceCard->is_active = true;
                $sourceCard->save();
            }

            $usage->forceFill([
                'status' => 'reversed',
            ])->save();

            $booking->forceFill([
                'customer_card_id' => $targetCard->getKey(),
                'customer_card_units_used' => max(1, $unitsUsed),
                'customer_card_discount' => $discountGross,
                'customer_card_discount_gross' => $discountGross,
                'customer_card_coverage_type' => $coverageValue,
                'payment_method' => PaymentMethodEnum::CUSTOMER_CARD(),
                'payment_split_card_gross' => $discountGross,
                'payment_split_online_gross' => max(0, (float) $booking->amount),
                'customer_card_consumed_at' => null,
            ])->save();

            $booking->refresh();

            $this->finalizeUsage($booking);

            return CustomerCardUsage::query()
                ->where('course_booking_id', $booking->getKey())
                ->where(function ($query) {
                    $query
                        ->whereNull('status')
                        ->orWhere('status', '!=', 'reversed');
                })
                ->latest()
                ->first();
        });
    }
}
