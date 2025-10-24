<?php

namespace Botble\Courses\Listeners;

use Botble\Base\Facades\EmailHandler;
use Botble\Courses\Events\CourseBookingChangedCourseOrSession;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

class SendCourseOrSessionChangedEmailListener implements ShouldQueue
{
    public function handle(CourseBookingChangedCourseOrSession $event): void
    {
        $booking = $event->courseBooking;
        $session = $booking->session;

        $sessionTime = 'N/A';
        if ($session && $session->start_date && $session->end_date) {
            $start = Carbon::parse($session->start_date);
            $end = Carbon::parse($session->end_date);

            if ($start->isSameDay($end)) {
                $sessionTime = $start->format('d-M-Y h:i A') . ' - ' . $end->format('h:i A');
            } else {
                $sessionTime = $start->format('d-M-Y h:i A') . ' - ' . $end->format('d-M-Y h:i A');
            }
        }

        if ($booking->customer && $booking->customer->email) {
            $recipientName = trim(($booking->customer->first_name ?? '') . ' ' . ($booking->customer->last_name ?? ''));
            $recipientEmail = $booking->customer->email;
        } else {
            $recipientName = trim(($booking->address->first_name ?? '') . ' ' . ($booking->address->last_name ?? ''));
            $recipientEmail = $booking->address->email;
        }
        EmailHandler::setModule(HOTEL_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'booking_name' => $recipientName ?: 'Customer',
                'course_name'  => $booking->course?->name ?? 'N/A',
                'session_name' => $sessionTime,
                'booking_link' => route('public.booking.information', $booking->transaction_id),
                'site_title'   => theme_option('site_title'),
            ])
            ->sendUsingTemplate('booking-course-or-session-changed', $recipientEmail);
    }
}
