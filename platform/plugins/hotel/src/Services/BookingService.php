<?php

namespace Botble\Hotel\Services;

use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Hotel\Events\BookingCreated;
use Botble\Hotel\Models\Booking;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;

class BookingService
{
    public function processBooking(int $bookingId, ?string $chargeId = null): ?Booking
    {
        /**
         * @var Booking $booking
         */
        $booking = Booking::query()->find($bookingId);

        $originalPaymentId = $booking?->payment_id;

        if (! $booking) {
            return null;
        }

        if ($chargeId && is_plugin_active('payment')) {
            $payment = Payment::query()->where(['charge_id' => $chargeId])->first();

            if ($payment) {
                $booking->payment_id = $payment->getKey();

                if ($payment->status == PaymentStatusEnum::COMPLETED) {
                    $booking->status = BookingStatusEnum::PROCESSING;
                }

                $booking->save();
            }
        }

        if ($originalPaymentId && $booking->payment_id === $originalPaymentId) {
            return $booking;
        }

        BookingCreated::dispatch($booking);

        return $booking;
    }
}
