<?php

namespace Botble\Courses\Services;

use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Courses\Events\CourseBookingCreated;
use Botble\Courses\Models\CourseBooking;
use Botble\Hotel\Enums\CustomerCardCoverageType;
use Botble\Hotel\Models\CustomerCard;
use Botble\Hotel\Services\CustomerCardPricingService;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class CourseBookingService
{
    public function processBooking(int $bookingId, ?string $chargeId = null): ?CourseBooking
    {
        /** @var CourseBooking|null $courseBooking */
        $courseBooking = CourseBooking::query()->find($bookingId);

        if (! $courseBooking) {
            return null;
        }

        $customerCardMethod = (string) PaymentMethodEnum::CUSTOMER_CARD();

        /** ----------------------------------------------------------------
         *  PAYMENT ERMITTELN
         * ---------------------------------------------------------------- */
        $payment = null;

        if ($chargeId) {
            $payment = Payment::query()->where(['charge_id' => $chargeId])->first();
        }

        if (! $payment && $courseBooking->payment_id) {
            $payment = Payment::query()->find($courseBooking->payment_id);
        }

        if (! $payment) {
            $payment = Payment::query()
                ->where([
                    'order_id' => $bookingId,
                    'order_type' => CourseBooking::class,
                ])
                ->latest()
                ->first();
        }

        if ($payment) {
            $courseBooking->payment_id = $payment->getKey();

            $channel = $this->normalizePaymentChannel($payment->payment_channel);
            $courseBooking->payment_method = $channel;

            /** ----------------------------------------------------------------
             *  ENUM STATUS SICHER LESEN
             * ---------------------------------------------------------------- */
            $status = $payment->status instanceof \BackedEnum
                ? $payment->status->value
                : (string) $payment->status;

            /** ----------------------------------------------------------------
             *  BOOKING STATUS HANDLING
             * ---------------------------------------------------------------- */
            switch ($status) {

                /** PAYMENT COMPLETED */
                case PaymentStatusEnum::COMPLETED->value:
                    if ($courseBooking->customer_card_id && ! $courseBooking->customer_card_consumed_at) {
                        $courseBooking->status = BookingStatusEnum::PENDING;
                    } else {
                        $courseBooking->status = BookingStatusEnum::PROCESSING;
                    }
                    break;

                /** PAYMENT PENDING */
                case PaymentStatusEnum::PENDING->value:
                    if (in_array($channel, ['cod', 'bank_transfer'])) {
                        $courseBooking->status = BookingStatusEnum::PENDING;
                    } else {
                        $courseBooking->status = BookingStatusEnum::AWAITING_PAYMENT;
                    }
                    break;

                /** PAYMENT FAILED */
                case PaymentStatusEnum::FAILED->value:
                case PaymentStatusEnum::FRAUD->value:
                case PaymentStatusEnum::CANCELED->value:
                    $courseBooking->status = BookingStatusEnum::FAILED;
                    break;

                /** PAYMENT REFUND */
                case PaymentStatusEnum::REFUNDING->value:
                case PaymentStatusEnum::REFUNDED->value:
                    $courseBooking->status = BookingStatusEnum::CANCELLED;
                    break;
            }

            $courseBooking->save();
        }

        /** ----------------------------------------------------------------
         *  PAYMENT METHOD ZWINGEND SETZEN
         * ---------------------------------------------------------------- */
        if ($courseBooking->customer_card_id && ! $courseBooking->payment_method) {
            $courseBooking->payment_method = $customerCardMethod;
            $courseBooking->save();
        }

        /** ----------------------------------------------------------------
         *  COVERAGE TYPE STANDARDISIEREN
         * ---------------------------------------------------------------- */
        if ($courseBooking->customer_card_id && ! $courseBooking->customer_card_coverage_type) {
            $courseBooking->customer_card_coverage_type = $this->normalizeCoverageType(
                CustomerCardCoverageType::PARTIAL
            );
            $courseBooking->save();
        }

        CourseBookingCreated::dispatch($courseBooking);

        return $courseBooking;
    }

    /** =========================================================================
     *  FINALIZE CUSTOMER CARD
     * =========================================================================*/
    public function finalizeCustomerCardUsage(CourseBooking $courseBooking): void
    {
        if (! $courseBooking->exists) {
            Log::warning('[CustomerCardFinalize] Booking not persisted, skipping', [
                'booking_id' => $courseBooking->getKey(),
            ]);
            return;
        }

        $customerCardMethod = $this->normalizePaymentChannel(PaymentMethodEnum::CUSTOMER_CARD());

        /** ---------------------------------------------------------------
         *  PAYMENT ERNEUT LADEN
         * --------------------------------------------------------------- */
        $payment = null;

        if ($courseBooking->payment_id) {
            $payment = Payment::query()->find($courseBooking->payment_id);
        }

        if (! $payment) {
            $payment = Payment::query()
                ->where([
                    'order_id' => $courseBooking->getKey(),
                    'order_type' => CourseBooking::class,
                ])
                ->latest()
                ->first();
        }

        /** ---------------------------------------------------------------
         *  PAYMENT STATUS ERMITTELN
         * --------------------------------------------------------------- */
        $paymentStatus = $payment?->status instanceof \BackedEnum
            ? $payment->status->value
            : (string) ($payment->status ?? 'unknown');

        /** ---------------------------------------------------------------
         *  WENN BETRAG > 0 → PAYMENT MUSS COMPLETED SEIN
         * --------------------------------------------------------------- */
        if ($courseBooking->amount > 0 && $paymentStatus !== PaymentStatusEnum::COMPLETED->value) {
            Log::warning('[CustomerCardFinalize] Payment not completed, skipping', [
                'booking_id' => $courseBooking->getKey(),
                'payment_status' => $paymentStatus,
            ]);
            return;
        }

        /** ---------------------------------------------------------------
         *  ZERO-AMOUNT → OK OHNE PAYMENT
         * --------------------------------------------------------------- */
        Log::info('[CustomerCardFinalize] Zero-amount allowed', [
            'booking_id' => $courseBooking->getKey(),
            'payment_status' => $paymentStatus,
        ]);

        /** ---------------------------------------------------------------
         *  UNITS VERFÜGBAR?
         * --------------------------------------------------------------- */
        if ($courseBooking->customer_card_units_used <= 0) {
            Log::warning('[CustomerCardFinalize] missing units_used');
            return;
        }

        if ($courseBooking->customer_card_consumed_at) {
            Log::info('[CustomerCardFinalize] already consumed');
            return;
        }

        /** ---------------------------------------------------------------
         *  TRANSACTION: CARD LOCK + USAGE
         * --------------------------------------------------------------- */
        DB::transaction(function () use ($courseBooking) {

            $card = CustomerCard::query()->lockForUpdate()->find($courseBooking->customer_card_id);

            if (! $card || $card->assigned_to !== $courseBooking->customer_id) {
                Log::warning('[CustomerCardFinalize] Card mismatch');
                return;
            }

            $unitsRequested = max(1, (int) $courseBooking->customer_card_units_used);

            if ($card->units_remaining < $unitsRequested) {
                Log::warning('[CustomerCardFinalize] insufficient units');
                return;
            }

            Log::info('[CustomerCardFinalize] Performing finalizeUsage');

            app(CustomerCardPricingService::class)->finalizeUsage($courseBooking);

            /** After consumption → booking is processing */
            if ($courseBooking->status !== BookingStatusEnum::PROCESSING) {
                $courseBooking->status = BookingStatusEnum::PROCESSING;
                $courseBooking->save();
            }
        });
    }

    /** ────────────────────────────────────────────────────────────────────────
     *  HELPER
     * ──────────────────────────────────────────────────────────────────────── */

    public function normalizePaymentChannel(mixed $channel): string
    {
        if ($channel instanceof PaymentMethodEnum) {
            return $channel->value;
        }

        return (string) $channel ?: 'unknown';
    }

    protected function normalizeCoverageType(mixed $coverageType): string
    {
        if ($coverageType instanceof CustomerCardCoverageType) {
            return $coverageType->value;
        }

        return (string) $coverageType ?: CustomerCardCoverageType::PARTIAL->value;
    }
}
