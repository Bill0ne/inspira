<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Courses\Models\CourseSession;
use Botble\Hotel\Models\ManualBooking;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Hotel\Models\Booking;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingReportRecordController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
        ]);

        $bookingRecordsQuery = Booking::query();

        do_action('booking_reports_before_get_records', $request);
        do_action('booking_reports_before_query', $bookingRecordsQuery);

        $startDate = $request->date('start');
        $endDate = $request->date('end');

        $bookingRecordsQuery
            ->with(['room.room', 'address', 'services', 'payment', 'invoice'])
            ->whereHas('room', function (Builder $query) use ($startDate, $endDate): void {
                $query
                    ->whereDate('start_date', '<=', $endDate)
                    ->whereDate('end_date', '>=', $startDate);
            })
            ->whereNot('status', BookingStatusEnum::CANCELLED);

        do_action('booking_reports_after_query', $bookingRecordsQuery);

        $bookingRecords = apply_filters('booking_reports_records', $bookingRecordsQuery->get());

        do_action('booking_reports_after_get_records', $bookingRecords);

        $json = $bookingRecords->map(function (Booking $booking) {
            return [
                'id' => 'room-' . $booking->getKey(),
                'textColor' => match ($booking->status->getValue()) {
                    'pending' => '#715a00',
                    'completed' => '#effeff',
                    'cancelled' => '#ffe0e2',
                    default => '#e7f1ff',
                },
                'backgroundColor' => match ($booking->status->getValue()) {
                    'pending' => '#ffc300',
                    'completed' => '#36c6d3',
                    'cancelled' => '#ed6b75',
                    default => '#0d6efd',
                },
                'borderColor' => 'transparent',
                'title' => trans('plugins/hotel::booking.calendar_item_title', [
                    'room' => $booking->room->room_name,
                    'number_of_rooms' => $booking->room->number_of_rooms,
                    'number_of_guests' => $booking->number_of_guests,
                    'number_of_children' => $booking->number_of_children,
                ]),
                'detail' => apply_filters('booking_reports_detail_render', view('plugins/hotel::booking-info', [
                    'booking' => $booking,
                    'displayBookingStatus' => true,
                ])->render(), $booking),
                'detailUrl' => route('booking.edit', $booking),
                'start' => $booking->room->start_date,
                'end' => $booking->room->end_date,
            ];
        })->values();

        $courseSessions = CourseSession::query()
            ->with('course')
            ->withCount([
                'bookings as booked_count' => function (Builder $query): void {
                    $query->whereIn('status', [
                        BookingStatusEnum::PENDING,
                        BookingStatusEnum::PROCESSING,
                        BookingStatusEnum::COMPLETED,
                    ]);
                },
            ])
            ->where(function (Builder $query) use ($startDate, $endDate): void {
                $query
                    ->whereDate('start_date', '<=', $endDate)
                    ->whereDate('end_date', '>=', $startDate);
            })
            ->get();

        $courseJson = $courseSessions->map(function (CourseSession $session) {
            $courseName = $session->course?->name ?? trans('plugins/courses::courses.course.name');
            $bookedCount = $session->booked_count ?? 0;
            $availableSeats = $session->available_seats ?? 0;
            $title = trans('plugins/courses::courses.calendar_item_title', [
                'course' => $courseName,
                'booked' => $bookedCount,
                'seats' => $availableSeats,
            ]);

            return [
                'id' => 'course-session-' . $session->getKey(),
                'textColor' => '#05264d',
                'backgroundColor' => '#9ecbff',
                'borderColor' => 'transparent',
                'title' => $title,
                'detail' => apply_filters('booking_reports_course_detail_render', view('plugins/courses::session-info', [
                    'session' => $session,
                ])->render(), $session),
                'detailUrl' => $session->course_id ? route('course.edit', $session->course_id) : null,
                'start' => $session->start_date,
                'end' => $session->end_date,
            ];
        });

        $manualBookings = ManualBooking::query()
            ->with(['room', 'course'])
            ->where(function (Builder $query) use ($startDate, $endDate): void {
                $query
                    ->whereDate('start_at', '<=', $endDate)
                    ->whereDate('end_at', '>=', $startDate);
            })
            ->get();

        $manualJson = $manualBookings->map(function (ManualBooking $booking) {
            $target = $booking->type === 'room'
                ? ($booking->room->name ?: trans('plugins/hotel::booking.room'))
                : ($booking->course->name ?: trans('plugins/courses::courses.course.name'));

            return [
                'id' => 'manual-' . $booking->getKey(),
                'textColor' => '#0f172a',
                'backgroundColor' => '#ffd966',
                'borderColor' => 'transparent',
                'title' => trans('plugins/hotel::booking.manual_booking_title', [
                    'target' => $target,
                ]),
                'detail' => view('plugins/hotel::manual-booking-info', [
                    'booking' => $booking,
                    'target' => $target,
                ])->render(),
                'detailUrl' => null,
                'start' => $booking->start_at,
                'end' => $booking->end_at,
            ];
        });

        return response()->json(
            apply_filters('booking_reports_records_json', $json->merge($courseJson)->merge($manualJson))
        );
    }
}
