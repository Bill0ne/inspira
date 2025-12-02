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
        return new CardEffectDTO(
            availableUnits: max($card->units_remaining, 0),
            baseAmountGross: max($baseAmountGross, 0),
            course: $course,
            card: $card
        );
    }

    public function finalizeUsage(CourseBooking $booking): void
    {
        if (!$booking->customer_card_id) {
            return;
        }

        DB::transaction(function () use ($booking) {

            $card = CustomerCard::query()->lockForUpdate()->find($booking->customer_card_id);

            Log::info('[CustomerCardFinalize] Start booking ' . $booking->getKey(), [
                'card_id'       => $booking->customer_card_id,
                'units_used'    => $booking->customer_card_units_used,
                'units_before'  => $card?->units_remaining,
            ]);

            if (!$card) {
                Log::warning('[CustomerCardFinalize] Card not found');
                return;
            }

            if ($booking->customer_id &&
                $card->assigned_to &&
                $card->assigned_to !== $booking->customer_id
            ) {
                Log::warning('[CustomerCardFinalize] Card assigned_to mismatch');
                return;
            }

            $usageExists = CustomerCardUsage::query()
                ->where('course_booking_id', $booking->getKey())
                ->where(fn ($q) => $q->whereNull('status')->orWhere('status', '!=', 'reversed'))
                ->exists();

            if ($usageExists) {
                if (!$booking->customer_card_consumed_at) {
                    $booking->forceFill(['customer_card_consumed_at' => now()])->save();
                }
                return;
            }

            $availableUnits = max(0, (int)$card->units_remaining);
            $unitsRequested = max(1, (int)$booking->customer_card_units_used);

            $units = min($unitsRequested, $availableUnits);
            $remainingUnits = $availableUnits - $units;

            $booking->forceFill([
                'customer_card_units_used' => $units
            ])->save();

            $card->forceFill([
                'units_remaining' => $remainingUnits,
                'is_active'       => $remainingUnits > 0,
            ])->save();

            $coverage = $booking->customer_card_coverage_type;
            $coverageValue = $coverage instanceof CustomerCardCoverageType
                ? $coverage->value
                : CustomerCardCoverageType::PARTIAL->value;

            CustomerCardUsage::query()->create([
                'card_id'           => $card->getKey(),
                'course_booking_id' => $booking->getKey(),
                'course_id'         => $booking->course_id,
                'units_used'        => $units,
                'discount_amount'   => (float)$booking->customer_card_discount_gross,
                'discount_gross'    => (float)$booking->customer_card_discount_gross,
                'coverage_type'     => $coverageValue,
                'status'            => 'consumed',
                'consumed_at'       => now(),
            ]);

            $booking->forceFill([
                'customer_card_consumed_at' => now(),
            ])->save();

            Log::info('[CustomerCardFinalize] Recorded usage for booking ' . $booking->getKey());
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

            if ($sourceCard) {
                $sourceCard->increment('units_remaining', $usage->units_used);
                $sourceCard->is_active = true;
                $sourceCard->save();
            }

            $usage->forceFill(['status' => 'reversed'])->save();

            $coverageValue = CustomerCardCoverageType::tryFrom($coverageType)?->value
                ?? $coverageType;

            $booking->forceFill([
                'customer_card_id'             => $targetCard->getKey(),
                'customer_card_units_used'     => max(1, $unitsUsed),
                'customer_card_discount'       => $discountGross,
                'customer_card_discount_gross' => $discountGross,
                'customer_card_coverage_type'  => $coverageValue,
                'payment_method'               => PaymentMethodEnum::CUSTOMER_CARD(),
                'payment_split_card_gross'     => $discountGross,
                'payment_split_online_gross'   => max(0, $booking->amount),
                'customer_card_consumed_at'    => null,
            ])->save();

            $booking->refresh();

            $this->finalizeUsage($booking);

            return CustomerCardUsage::query()
                ->where('course_booking_id', $booking->getKey())
                ->where(fn ($q) => $q->whereNull('status')->orWhere('status', '!=', 'reversed'))
                ->latest()
                ->first();
        });
    }
}
