<?php

namespace Botble\InspiraCancellation\Services;

use Botble\InspiraCancellation\Models\Cancellation;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Botble\Stripe\Services\Gateways\StripePaymentService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CancellationRefundService
{
    protected array $stripePaymentCache = [];

    public function __construct(
        protected CancellationService $cancellationService,
        protected StripePaymentService $stripePaymentService
    ) {
    }

    public function canProcessStripeRefund(Cancellation $cancellation): bool
    {
        if (! defined('STRIPE_PAYMENT_METHOD_NAME')) {
            return false;
        }

        return (bool) $this->getStripePayment($cancellation);
    }

    public function processStripeRefund(Cancellation $cancellation): array
    {
        if (! defined('STRIPE_PAYMENT_METHOD_NAME')) {
            return [
                'error' => true,
                'message' => trans('plugins/inspira-cancellation::cancellation.actions.stripe_paid_not_configured'),
            ];
        }

        $booking = $this->cancellationService->getBookingForCancellation($cancellation);

        if (! $booking) {
            return [
                'error' => true,
                'message' => trans('plugins/inspira-cancellation::cancellation.actions.stripe_paid_missing_booking'),
            ];
        }

        $payment = $this->getStripePayment($cancellation, $booking);

        if (! $payment) {
            return [
                'error' => true,
                'message' => trans('plugins/inspira-cancellation::cancellation.actions.stripe_paid_missing_payment'),
            ];
        }

        $currency = strtoupper($payment->currency ?: config('app.currency', 'EUR'));

        $this->stripePaymentService->setCurrency($currency);

        $result = $this->stripePaymentService->refundOrder(
            $payment->charge_id,
            $cancellation->refund_amount,
            [
                'cancellation_id' => $cancellation->getKey(),
                'booking_id' => $cancellation->booking_id,
                'booking_type' => $cancellation->booking_type,
            ]
        );

        if (Arr::get($result, 'error')) {
            return [
                'error' => true,
                'message' => Arr::get($result, 'message') ?: trans('plugins/inspira-cancellation::cancellation.actions.stripe_paid_failed'),
            ];
        }

        $refundData = Arr::get($result, 'data', []);
        $refundId = Arr::get($refundData, '_refund_id') ?? Arr::get($refundData, 'id');

        $metadata = $payment->metadata ?? [];
        $refunds = Arr::get($metadata, 'refunds', []);
        $refundEntry = array_merge($refundData, [
            '_refund_id' => $refundId,
            'refund_amount' => $cancellation->refund_amount,
            'refund_currency' => $payment->currency,
            'cancellation_id' => $cancellation->getKey(),
            'processed_via' => STRIPE_PAYMENT_METHOD_NAME,
            'processed_at' => now()->toDateTimeString(),
        ]);
        $refunds[] = $refundEntry;

        Arr::set($metadata, 'refunds', $refunds);
        $payment->metadata = $metadata;
        $payment->refunded_amount = ($payment->refunded_amount ?? 0) + $cancellation->refund_amount;

        $payment->status = $payment->refunded_amount >= $payment->amount
            ? PaymentStatusEnum::REFUNDED
            : PaymentStatusEnum::REFUNDING;

        $payment->save();

        return [
            'error' => false,
            'refund' => $refundEntry,
            'payment' => $payment,
        ];
    }

    protected function getStripePayment(Cancellation $cancellation, ?Model $booking = null): ?Payment
    {
        if (array_key_exists($cancellation->getKey(), $this->stripePaymentCache)) {
            return $this->stripePaymentCache[$cancellation->getKey()];
        }

        return $this->stripePaymentCache[$cancellation->getKey()] = $this->resolveStripePayment($cancellation, $booking);
    }

    protected function resolveStripePayment(Cancellation $cancellation, ?Model $booking = null): ?Payment
    {
        $booking = $booking ?: $this->cancellationService->getBookingForCancellation($cancellation);

        if (! $booking || ! method_exists($booking, 'payment')) {
            return null;
        }

        $payment = $booking->payment;

        if (! $payment || ! $payment->getKey()) {
            return null;
        }

        if ((string) $payment->payment_channel !== STRIPE_PAYMENT_METHOD_NAME) {
            return null;
        }

        if (! $payment->charge_id) {
            return null;
        }

        return $payment;
    }
}
