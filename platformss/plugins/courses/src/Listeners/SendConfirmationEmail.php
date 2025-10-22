<?php

namespace Botble\Courses\Listeners;

use Botble\Base\Facades\EmailHandler;
use Botble\Courses\Events\CourseBookingCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendConfirmationEmail implements ShouldQueue
{
    public function handle(CourseBookingCreated $event): void
    {
        $courseBooking = $event->courseBooking;

        $address = '';

        if ($courseBooking->address->id) {
            if ($courseBooking->address->address) {
                $address .= $courseBooking->address->address . ', ';
            }

            if ($courseBooking->address->city) {
                $address .= $courseBooking->address->city . ', ';
            }

            if ($courseBooking->address->state) {
                $address .= $courseBooking->address->state . ', ';
            }

            if ($courseBooking->address->country) {
                $address .= $courseBooking->address->country . ', ';
            }

            if ($courseBooking->address->zip) {
                $address .= $courseBooking->address->zip;
            }
        } else {
            $address = 'N/A';
        }

        $address = rtrim($address, ', ');

        EmailHandler::setModule(HOTEL_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'booking_type' => 'Kurse',
                'booking_name' => $courseBooking->address->first_name ? $courseBooking->address->first_name . ' ' . $courseBooking->address->last_name : 'N/A',
                'booking_email' => $courseBooking->address->email ?? 'N/A',
                'booking_phone' => $courseBooking->address->phone ?? 'N/A',
                'booking_address' => $address,
                'booking_request' => $courseBooking->requests,
                'booking_link' => route('public.course.booking.information', $courseBooking->transaction_id),
            ]);

        EmailHandler::sendUsingTemplate('booking-confirmation', $courseBooking->address->email);
        EmailHandler::sendUsingTemplate('booking-notice-to-admin');
    }
}
