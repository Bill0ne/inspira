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

class CourseBookingService
{
    public function processBooking(int $bookingId, ?string $chargeId = null): ?CourseBooking
    {
        /**
         * @var CourseBooking|null $courseBooking
         */
        $courseBooking = CourseBooking::query()->find($bookingId);

        if (! $courseBooking) {
            return null;
        }

        if (is_plugin_active('payment')) {
            $payment = null;

            if ($chargeId) {
                $payment = Payment::query()->where(['charge_id' => $chargeId])->first();
            }

            if (! $payment && $courseBooking->payment_id) {
                $payment = Payment::query()->find($courseBooking->payment_id);
            }

            if (! $payment) {
                $payment = Payment::query()
                    ->where(['order_id' => $bookingId, 'order_type' => CourseBooking::class])
                    ->latest()
                    ->first();
            }

            if ($payment) {
                $courseBooking->payment_id = $payment->getKey();
                $courseBooking->payment_method = $payment->payment_channel;

                $method = (string) $payment->payment_channel;

                switch ($payment->status) {
                    case PaymentStatusEnum::COMPLETED:
                        $courseBooking->status = BookingStatusEnum::PROCESSING;
                        break;

                    case PaymentStatusEnum::PENDING:
                        if (in_array($method, ['cod', 'bank_transfer'])) {
                            $courseBooking->status = BookingStatusEnum::PENDING;
                        } else {
                            $courseBooking->status = BookingStatusEnum::AWAITING_PAYMENT;
                        }
                        break;

                    case PaymentStatusEnum::FAILED:
                    case PaymentStatusEnum::FRAUD:
                    case PaymentStatusEnum::CANCELED:
                        $courseBooking->status = BookingStatusEnum::FAILED;
                        break;

                    case PaymentStatusEnum::REFUNDING:
                    case PaymentStatusEnum::REFUNDED:
                        $courseBooking->status = BookingStatusEnum::CANCELLED;
                        break;
                }

                $courseBooking->save();
            }

            if (
                $courseBooking->payment_method === PaymentMethodEnum::CUSTOMER_CARD()
                && $courseBooking->status !== BookingStatusEnum::PROCESSING
            ) {
                $courseBooking->status = BookingStatusEnum::PROCESSING;
                $courseBooking->save();
            }
        }

        $this->finalizeCustomerCardUsage($courseBooking);

        CourseBookingCreated::dispatch($courseBooking);

        return $courseBooking;
    }

    public function finalizeCustomerCardUsage(CourseBooking $courseBooking): void
    {
        if ($courseBooking->customer_card_consumed_at) {
            Log::info('[CustomerCardFinalize] Booking ' . $courseBooking->getKey() . ' already finalized');

            return;
        }

        if (
            $courseBooking->status !== BookingStatusEnum::PROCESSING
            || ! $courseBooking->customer_card_id
            || $courseBooking->customer_card_units_used <= 0
        ) {
            Log::info('[CustomerCardFinalize] Skipping booking ' . $courseBooking->getKey() . ' because it is not ready', [
                'status' => (string) $courseBooking->status,
                'card_id' => $courseBooking->customer_card_id,
                'units_used' => $courseBooking->customer_card_units_used,
            ]);

            return;
        }

        if (! $courseBooking->customer_card_coverage_type) {
            $courseBooking->customer_card_coverage_type = CustomerCardCoverageType::PARTIAL;
        }

        Log::info('[CustomerCardFinalize] Starting usage for booking ' . $courseBooking->getKey(), [
            'card_id' => $courseBooking->customer_card_id,
            'units_used' => $courseBooking->customer_card_units_used,
            'coverage' => (string) $courseBooking->customer_card_coverage_type,
        ]);

        DB::transaction(function () use ($courseBooking) {
            $card = CustomerCard::query()->lockForUpdate()->find($courseBooking->customer_card_id);

            if ($card && $courseBooking->customer_id && $card->assigned_to !== $courseBooking->customer_id) {
                $card = null;
            }

            if (! $card) {
                Log::warning('[CustomerCardFinalize] Card not found or mismatched for booking ' . $courseBooking->getKey());

                return;
            }

            $unitsRequested = max(1, (int) $courseBooking->customer_card_units_used);

            if ($card->units_remaining < $unitsRequested) {
                Log::warning('[CustomerCardFinalize] Booking ' . $courseBooking->getKey() . ' requires '
                    . $unitsRequested . ' units but card ' . $card->getKey() . ' has '
                    . $card->units_remaining . ' remaining');

                return;
            }

            Log::info('[CustomerCardFinalize] Booking ' . $courseBooking->getKey() . ' finalizing with card ' . $card->getKey());

            app(CustomerCardPricingService::class)->finalizeUsage($courseBooking);

            Log::info('[CustomerCardFinalize] Booking ' . $courseBooking->getKey() . ' finalized with card '
                . $card->getKey());
        });
    }
}
