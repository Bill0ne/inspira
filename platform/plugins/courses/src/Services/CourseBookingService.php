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

        $customerCardMethod = (string) PaymentMethodEnum::CUSTOMER_CARD();

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
                $channel = $this->normalizePaymentChannel($payment->payment_channel);
                $courseBooking->payment_method = $channel;

                $method = $channel;

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
                (string) $courseBooking->payment_method === $customerCardMethod
                && $courseBooking->status !== BookingStatusEnum::PROCESSING
            ) {
                $courseBooking->status = BookingStatusEnum::PROCESSING;
                $courseBooking->save();
            }
        }

        if ($courseBooking->customer_card_id && ! $courseBooking->payment_method) {
            $courseBooking->payment_method = $customerCardMethod;
            $courseBooking->save();
        }

        if ($courseBooking->customer_card_id && $courseBooking->status !== BookingStatusEnum::PROCESSING) {
            $courseBooking->status = BookingStatusEnum::PROCESSING;
            $courseBooking->save();
        }

        if ($courseBooking->customer_card_id && ! $courseBooking->customer_card_coverage_type) {
            $courseBooking->customer_card_coverage_type = $this->normalizeCoverageType(
                CustomerCardCoverageType::PARTIAL
            );
            $courseBooking->save();
        }

        CourseBookingCreated::dispatch($courseBooking);

        return $courseBooking;
    }

    public function finalizeCustomerCardUsage(CourseBooking $courseBooking): void
    {
        if (! $courseBooking->exists) {
            Log::warning('[CustomerCardFinalize] Booking not persisted, skipping', [
                'booking_id' => $courseBooking->getKey(),
            ]);

            return;
        }

        $customerCardMethod = $this->normalizePaymentChannel(PaymentMethodEnum::CUSTOMER_CARD());

        if ($courseBooking->customer_card_id && ! $courseBooking->payment_method) {
            $courseBooking->payment_method = $customerCardMethod;
            $courseBooking->save();
        }

        $paymentId = $courseBooking->payment_id;
        $payment = null;

        if ($paymentId) {
            $payment = Payment::query()->find($paymentId);

            if ($payment && $payment->status !== PaymentStatusEnum::COMPLETED && $courseBooking->amount > 0) {
                Log::warning('[CustomerCardFinalize] Payment not completed yet, skipping', [
                    'booking_id' => $courseBooking->getKey(),
                    'payment_id' => $paymentId,
                    'payment_status' => $payment->status->value ?? $payment->status,
                ]);

                return;
            }
        }

        $isCustomerCardPayment = $this->normalizePaymentChannel($courseBooking->payment_method) === $customerCardMethod;

        if (! $payment && ! $isCustomerCardPayment && $courseBooking->amount > 0) {
            Log::warning('[CustomerCardFinalize] Booking missing completed payment, skipping', [
                'booking_id' => $courseBooking->getKey(),
                'payment_id' => $paymentId,
            ]);

            return;
        }

        if ($courseBooking->customer_card_units_used <= 0) {
            $courseBooking->customer_card_units_used = 1;
            $courseBooking->save();
        }

        Log::info('[CustomerCardFinalize] Start booking ' . $courseBooking->getKey(), [
            'status' => $this->resolveEnumValue($courseBooking->status),
            'payment_method' => $this->resolveEnumValue($courseBooking->payment_method),
            'payment_id' => $courseBooking->payment_id,
        ]);

        if ($courseBooking->customer_card_id && $courseBooking->status !== BookingStatusEnum::PROCESSING) {
            $courseBooking->status = BookingStatusEnum::PROCESSING;
            $courseBooking->save();

            Log::info('[CustomerCardFinalize] Forced booking to PROCESSING due to customer card usage', [
                'booking_id' => $courseBooking->getKey(),
            ]);
        }

        if ($courseBooking->customer_card_consumed_at) {
            Log::info('[CustomerCardFinalize] Booking ' . $courseBooking->getKey() . ' already finalized');

            return;
        }

        if (! $courseBooking->customer_card_id || $courseBooking->customer_card_units_used <= 0) {
            Log::info('[CustomerCardFinalize] Skipping booking ' . $courseBooking->getKey() . ' because it is not ready', [
                'status' => $this->resolveEnumValue($courseBooking->status),
                'card_id' => $courseBooking->customer_card_id,
                'units_used' => $courseBooking->customer_card_units_used,
            ]);

            return;
        }

        $coverageValue = $this->normalizeCoverageType($courseBooking->customer_card_coverage_type);

        if ($courseBooking->customer_card_coverage_type !== $coverageValue) {
            $courseBooking->customer_card_coverage_type = $coverageValue;
            $courseBooking->save();
        }

        Log::info('[CustomerCardFinalize] Starting usage for booking ' . $courseBooking->getKey(), [
            'card_id' => $courseBooking->customer_card_id,
            'units_used' => $courseBooking->customer_card_units_used,
            'coverage' => $coverageValue,
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

    public function normalizePaymentChannel(mixed $channel): string
    {
        if ($channel instanceof PaymentMethodEnum) {
            $channel = $channel->getValue();
        }

        if (empty($channel) || (! is_string($channel) && ! is_numeric($channel))) {
            return 'unknown';
        }

        return (string) $channel;
    }

    protected function resolveEnumValue(mixed $enum, string $default = 'unknown'): string
    {
        if ($enum instanceof \BackedEnum) {
            return (string) $enum->value;
        }

        if ($enum instanceof \UnitEnum) {
            return $enum->name;
        }

        if (is_object($enum) && method_exists($enum, 'getValue')) {
            return (string) $enum->getValue();
        }

        if (is_string($enum) || is_numeric($enum)) {
            return (string) $enum;
        }

        return $default;
    }

    protected function normalizeCoverageType(mixed $coverageType): string
    {
        if ($coverageType instanceof CustomerCardCoverageType) {
            return $coverageType->value;
        }

        if (is_string($coverageType) || is_numeric($coverageType)) {
            return (string) $coverageType;
        }

        return CustomerCardCoverageType::PARTIAL->value;
    }
}
