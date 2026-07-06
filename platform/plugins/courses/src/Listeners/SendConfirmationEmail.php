<?php

namespace Botble\Courses\Listeners;

use Botble\Base\Facades\EmailHandler;
use Botble\Courses\Events\CourseBookingCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendConfirmationEmail implements ShouldQueue
{
    /**
     * Dedicated email module screen name for course emails.
     *
     * NOTE: We intentionally use the literal 'courses' (matching the plugin
     * folder) instead of COURSE_MODULE_SCREEN_NAME ('course', singular), because
     * the email template loader resolves the body via
     * platform_path('plugins/{module}/resources/email-templates/{key}.tpl').
     * With 'course' that path would not exist and the body would render empty.
     */
    protected const EMAIL_MODULE = 'courses';

    public function handle(CourseBookingCreated $event): void
    {
        $courseBooking = $event->courseBooking;

        // Make sure the relations used below are available in the queue worker.
        $courseBooking->loadMissing(['course', 'session', 'address']);

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

        [$sessionName, $sessionDate] = $this->resolveSession($courseBooking);

        $variables = [
            'booking_type' => 'Kurse',
            'booking_name' => $courseBooking->address->first_name
                ? $courseBooking->address->first_name . ' ' . $courseBooking->address->last_name
                : 'N/A',
            'booking_email' => $courseBooking->address->email ?? 'N/A',
            'booking_phone' => $courseBooking->address->phone ?? 'N/A',
            'booking_address' => $address,
            'booking_request' => $courseBooking->requests,
            'booking_link' => route('public.course.booking.information', $courseBooking->transaction_id),
        ];

        // Customer confirmation via the dedicated course template (incl. course + date).
        // Self-register the settings so template variables are resolved even when the
        // RouteMatched hook did not run (e.g. queued worker).
        EmailHandler::addTemplateSettings(self::EMAIL_MODULE, config('plugins.courses.email', []))
            ->setModule(self::EMAIL_MODULE)
            ->setVariableValues($variables + [
                'course_name' => $courseBooking->course?->name ?: 'N/A',
                'session_name' => $sessionName,
                'session_date' => $sessionDate,
            ])
            ->sendUsingTemplate('course-booking-confirmation', $courseBooking->address->email);

        // Admin notice keeps using the shared hotel template.
        EmailHandler::setModule(HOTEL_MODULE_SCREEN_NAME)
            ->setVariableValues($variables)
            ->sendUsingTemplate('booking-notice-to-admin');
    }

    /**
     * Build a German-formatted session label (range) and start date/time.
     *
     * @return array{0: string, 1: string} [session_name, session_date]
     */
    protected function resolveSession($courseBooking): array
    {
        $session = $courseBooking->session;

        if (! $session || ! $session->start_date) {
            return ['', ''];
        }

        $start = $session->start_date;
        $sessionDate = $start->format('d.m.Y H:i') . ' Uhr';

        if (! $session->end_date) {
            return [$sessionDate, $sessionDate];
        }

        $end = $session->end_date;

        $sessionName = $start->isSameDay($end)
            ? $start->format('d.m.Y H:i') . ' – ' . $end->format('H:i') . ' Uhr'
            : $start->format('d.m.Y H:i') . ' – ' . $end->format('d.m.Y H:i') . ' Uhr';

        return [$sessionName, $sessionDate];
    }
}
