<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Courses\Models\CourseSession;
use Botble\Hotel\Models\ManualBooking;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Hotel\Models\Booking;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

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
            $guests = ($booking->number_of_guests ?: 0) + ($booking->number_of_children ?: 0);
            $roomName = $booking->room->room_name ?? __('Room');

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
                'title' => "\u{1F3E8} " . $roomName . ' · ' . $guests . "\u{1F464}",
                'start' => $booking->room->start_date,
                'end' => $booking->room->end_date,
                'extendedProps' => [
                    'cardType' => 'room',
                    'name' => $roomName,
                    'detail' => apply_filters('booking_reports_detail_render', view('plugins/hotel::booking-info', [
                        'booking' => $booking,
                        'displayBookingStatus' => true,
                    ])->render(), $booking),
                    'detailUrl' => route('booking.edit', $booking),
                    'status' => $booking->status->label(),
                    'statusColor' => match ($booking->status->getValue()) {
                        'pending' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'processing' => 'info',
                        'awaiting_payment' => 'primary',
                        default => 'secondary',
                    },
                    'dateRange' => $booking->room
                        ? Carbon::parse($booking->room->start_date)->format('d.m.Y') . ' - ' . Carbon::parse($booking->room->end_date)->format('d.m.Y')
                        : '',
                    'guests' => $booking->number_of_guests ?: 0,
                    'children' => $booking->number_of_children ?: 0,
                    'amount' => $booking->amount ? format_price($booking->amount) : null,
                    'bookingNumber' => $booking->booking_number,
                ],
            ];
        })->values();

        $courseSessions = CourseSession::query()
            ->with(['course', 'course.instructor', 'course.room'])
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
            $instructorName = $session->course?->instructor?->name ?? null;
            $roomName = $session->course?->room?->name ?? null;

            return [
                'id' => 'course-session-' . $session->getKey(),
                'textColor' => '#05264d',
                'backgroundColor' => '#9ecbff',
                'borderColor' => 'transparent',
                'title' => "\u{1F4DA} " . $courseName . ' · ' . $bookedCount . '/' . $availableSeats . "\u{1F464}",
                'start' => $session->start_date,
                'end' => $session->end_date,
                'extendedProps' => [
                    'cardType' => 'course',
                    'name' => $courseName,
                    'detail' => apply_filters('booking_reports_course_detail_render', view('plugins/courses::session-info', [
                        'session' => $session,
                    ])->render(), $session),
                    'detailUrl' => $session->course_id ? route('course.edit', $session->course_id) : null,
                    'status' => __('Geplant'),
                    'statusColor' => 'info',
                    'dateRange' => Carbon::parse($session->start_date)->format('d.m.Y, H:i') . ' - ' . Carbon::parse($session->end_date)->format('H:i'),
                    'bookedSeats' => $bookedCount,
                    'availableSeats' => $availableSeats,
                    'room' => $roomName,
                    'instructor' => $instructorName,
                ],
            ];
        });

        $manualBookings = Schema::hasTable('ht_manual_bookings')
            ? ManualBooking::query()
                ->with(['room', 'course'])
                ->where(function (Builder $query) use ($startDate, $endDate): void {
                    $query
                        ->whereDate('start_at', '<=', $endDate)
                        ->whereDate('end_at', '>=', $startDate);
                })
                ->get()
            : collect();

        $manualJson = $manualBookings->map(function (ManualBooking $booking) {
            $target = $booking->type === 'room'
                ? ($booking->room->name ?: trans('plugins/hotel::booking.room'))
                : ($booking->course->name ?: trans('plugins/courses::courses.course.name'));

            return [
                'id' => 'manual-' . $booking->getKey(),
                'textColor' => '#0f172a',
                'backgroundColor' => '#ffd966',
                'borderColor' => 'transparent',
                'title' => "\u{1F4DD} " . $target,
                'start' => $booking->start_at,
                'end' => $booking->end_at,
                'extendedProps' => [
                    'cardType' => 'manual',
                    'name' => $target,
                    'detail' => view('plugins/hotel::manual-booking-info', [
                        'booking' => $booking,
                        'target' => $target,
                    ])->render(),
                    'detailUrl' => null,
                    'status' => __('Manuell'),
                    'statusColor' => 'warning',
                    'dateRange' => Carbon::parse($booking->start_at)->format('d.m.Y, H:i') . ' - ' . Carbon::parse($booking->end_at)->format('d.m.Y, H:i'),
                    'reason' => $booking->reason,
                    'type' => $booking->type === 'room' ? __('Raum') : __('Kurs'),
                ],
            ];
        });

        return response()->json(
            apply_filters('booking_reports_records_json', $json->merge($courseJson)->merge($manualJson))
        );
    }
}
