<?php

namespace Botble\Courses\Listeners;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\EmailHandler;
use Botble\Courses\Events\CourseBookingChangedStatus;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCourseStatusChangedNotificationListener implements ShouldQueue
{
    public function handle(CourseBookingChangedStatus $event): void
    {
        $courseBooking = $event->courseBooking;

        EmailHandler::setModule(HOTEL_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'booking_type' => 'Kurse',
                'booking_name' => $courseBooking->address->first_name ? $courseBooking->address->first_name . ' ' . $courseBooking->address->last_name : 'N/A',
                'booking_date' => BaseHelper::formatDateTime($courseBooking->created_at),
                'booking_status' => $courseBooking->status->label(),
                'booking_link' => route('public.course.booking.information', $courseBooking->transaction_id),
            ])
            ->sendUsingTemplate('booking-status-changed', $courseBooking->address->email);
    }
}
