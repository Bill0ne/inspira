<?php

namespace Botble\Courses\Services;

use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Courses\Events\CourseBookingCreated;
use Botble\Courses\Models\CourseBooking;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;

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


        if ($chargeId && is_plugin_active('payment')) {
            $payment = Payment::query()->where(['charge_id' => $chargeId])->first();

            if ($payment) {
                $courseBooking->payment_id = $payment->getKey();

                $method = $payment->payment_channel;

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
        }

        CourseBookingCreated::dispatch($courseBooking);

        return $courseBooking;
    }
}
