<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Courses\Models\CourseSession;
use Botble\Hotel\Models\BookingRoom;
use Botble\Hotel\Models\ManualBooking;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Hotel\Models\Booking;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class BookingReportRecordController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
        ]);

        $startDate = $request->date('start');
        $endDate = $request->date('end');

        $events = collect();

        // ----------------------------------------------------------------
        // 1) Raumbuchungen — Multi-Slot-fähig (ein Event pro BookingRoom)
        // ----------------------------------------------------------------
        do_action('booking_reports_before_get_records', $request);

        $bookingRoomsQuery = BookingRoom::query()
            ->with([
                'booking',
                'booking.address',
                'booking.payment',
                'booking.invoice',
                'booking.services',
                'room',
            ])
            ->whereHas('booking', fn (Builder $q) => $q->whereNot('status', BookingStatusEnum::CANCELLED))
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate);

        do_action('booking_reports_before_query', $bookingRoomsQuery);

        $bookingRooms = apply_filters('booking_reports_records', $bookingRoomsQuery->get());

        do_action('booking_reports_after_query', $bookingRoomsQuery);
        do_action('booking_reports_after_get_records', $bookingRooms);

        foreach ($bookingRooms as $bookingRoom) {
            try {
                $booking = $bookingRoom->booking;
                if (! $booking || ! $booking->exists) {
                    continue;
                }

                $event = $this->mapBookingRoomToEvent($bookingRoom, $booking);
                if ($event !== null) {
                    $events->push($event);
                }
            } catch (Throwable $e) {
                Log::warning('[BookingCalendar] Konnte BookingRoom #' . $bookingRoom->getKey() . ' nicht rendern: ' . $e->getMessage());
            }
        }

        // ----------------------------------------------------------------
        // 2) Kurs-Sessions
        // ----------------------------------------------------------------
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

        foreach ($courseSessions as $session) {
            try {
                $event = $this->mapCourseSessionToEvent($session);
                if ($event !== null) {
                    $events->push($event);
                }
            } catch (Throwable $e) {
                Log::warning('[BookingCalendar] Konnte CourseSession #' . $session->getKey() . ' nicht rendern: ' . $e->getMessage());
            }
        }

        // ----------------------------------------------------------------
        // 3) Manuelle Buchungen (defensiv, falls Tabelle fehlt)
        // ----------------------------------------------------------------
        if (Schema::hasTable('ht_manual_bookings')) {
            $manualBookings = ManualBooking::query()
                ->with(['room', 'course'])
                ->where('start_at', '<=', $endDate)
                ->where('end_at', '>=', $startDate)
                ->get();

            foreach ($manualBookings as $manualBooking) {
                try {
                    $event = $this->mapManualBookingToEvent($manualBooking);
                    if ($event !== null) {
                        $events->push($event);
                    }
                } catch (Throwable $e) {
                    Log::warning('[BookingCalendar] Konnte ManualBooking #' . $manualBooking->getKey() . ' nicht rendern: ' . $e->getMessage());
                }
            }
        }

        return response()->json(
            apply_filters('booking_reports_records_json', $events->values())
        );
    }

    protected function mapBookingRoomToEvent(BookingRoom $bookingRoom, Booking $booking): ?array
    {
        if (! $bookingRoom->start_date || ! $bookingRoom->end_date) {
            return null;
        }

        $statusValue = $this->safeStatusValue($booking);
        $guests = (int) ($booking->number_of_guests ?: 0) + (int) ($booking->number_of_children ?: 0);
        $roomName = $bookingRoom->room_name ?: ($bookingRoom->room->name ?? __('Room'));

        return [
            'id' => 'room-booking-' . $bookingRoom->getKey(),
            'textColor' => match ($statusValue) {
                'pending' => '#715a00',
                'completed' => '#effeff',
                'cancelled' => '#ffe0e2',
                default => '#e7f1ff',
            },
            'backgroundColor' => match ($statusValue) {
                'pending' => '#ffc300',
                'completed' => '#36c6d3',
                'cancelled' => '#ed6b75',
                default => '#0d6efd',
            },
            'borderColor' => 'transparent',
            'title' => "\u{1F3E8} " . $roomName . ($guests > 0 ? ' · ' . $guests . "\u{1F464}" : ''),
            'start' => Carbon::parse($bookingRoom->start_date)->toIso8601String(),
            'end' => Carbon::parse($bookingRoom->end_date)->toIso8601String(),
            'extendedProps' => [
                'cardType' => 'room',
                'name' => $roomName,
                'detail' => $this->renderBookingDetail($booking),
                'detailUrl' => $this->safeRoute('booking.edit', $booking->getKey()),
                'status' => $this->safeStatusLabel($booking),
                'statusColor' => match ($statusValue) {
                    'pending' => 'warning',
                    'completed' => 'success',
                    'cancelled' => 'danger',
                    'processing' => 'info',
                    'awaiting_payment' => 'primary',
                    default => 'secondary',
                },
                'dateRange' => Carbon::parse($bookingRoom->start_date)->format('d.m.Y H:i')
                    . ' - ' . Carbon::parse($bookingRoom->end_date)->format('d.m.Y H:i'),
                'guests' => (int) ($booking->number_of_guests ?: 0),
                'children' => (int) ($booking->number_of_children ?: 0),
                'amount' => $booking->amount ? format_price($booking->amount) : null,
                'bookingNumber' => $booking->booking_number,
            ],
        ];
    }

    protected function mapCourseSessionToEvent(CourseSession $session): ?array
    {
        if (! $session->start_date || ! $session->end_date) {
            return null;
        }

        $courseName = $session->course?->name ?? trans('plugins/courses::courses.course.name');
        $bookedCount = (int) ($session->booked_count ?? 0);
        $availableSeats = (int) ($session->available_seats ?? 0);

        return [
            'id' => 'course-session-' . $session->getKey(),
            'textColor' => '#05264d',
            'backgroundColor' => '#9ecbff',
            'borderColor' => 'transparent',
            'title' => "\u{1F4DA} " . $courseName . ' · ' . $bookedCount . '/' . $availableSeats . "\u{1F464}",
            'start' => Carbon::parse($session->start_date)->toIso8601String(),
            'end' => Carbon::parse($session->end_date)->toIso8601String(),
            'extendedProps' => [
                'cardType' => 'course',
                'name' => $courseName,
                'detail' => $this->renderCourseDetail($session),
                'detailUrl' => $session->course_id ? $this->safeRoute('course.edit', $session->course_id) : null,
                'status' => __('Geplant'),
                'statusColor' => 'info',
                'dateRange' => Carbon::parse($session->start_date)->format('d.m.Y, H:i')
                    . ' - ' . Carbon::parse($session->end_date)->format('H:i'),
                'bookedSeats' => $bookedCount,
                'availableSeats' => $availableSeats,
                'room' => $session->course?->room?->name,
                'instructor' => $session->course?->instructor?->name,
            ],
        ];
    }

    protected function mapManualBookingToEvent(ManualBooking $booking): ?array
    {
        if (! $booking->start_at || ! $booking->end_at) {
            return null;
        }

        $target = $booking->type === 'room'
            ? ($booking->room?->name ?: trans('plugins/hotel::booking.room'))
            : ($booking->course?->name ?: trans('plugins/courses::courses.course.name'));

        $detail = '';
        try {
            $detail = view('plugins/hotel::manual-booking-info', [
                'booking' => $booking,
                'target' => $target,
            ])->render();
        } catch (Throwable $e) {
            $detail = '<div class="text-muted">' . e($booking->reason ?? '') . '</div>';
        }

        return [
            'id' => 'manual-' . $booking->getKey(),
            'textColor' => '#0f172a',
            'backgroundColor' => '#ffd966',
            'borderColor' => 'transparent',
            'title' => "\u{1F4DD} " . $target,
            'start' => Carbon::parse($booking->start_at)->toIso8601String(),
            'end' => Carbon::parse($booking->end_at)->toIso8601String(),
            'extendedProps' => [
                'cardType' => 'manual',
                'name' => $target,
                'detail' => $detail,
                'detailUrl' => null,
                'status' => __('Manuell'),
                'statusColor' => 'warning',
                'dateRange' => Carbon::parse($booking->start_at)->format('d.m.Y, H:i')
                    . ' - ' . Carbon::parse($booking->end_at)->format('d.m.Y, H:i'),
                'reason' => $booking->reason,
                'type' => $booking->type === 'room' ? __('Raum') : __('Kurs'),
            ],
        ];
    }

    protected function renderBookingDetail(Booking $booking): string
    {
        try {
            return apply_filters('booking_reports_detail_render', view('plugins/hotel::booking-info', [
                'booking' => $booking,
                'displayBookingStatus' => true,
            ])->render(), $booking);
        } catch (Throwable $e) {
            Log::warning('[BookingCalendar] booking-info-View für Buchung #' . $booking->getKey() . ' fehlgeschlagen: ' . $e->getMessage());

            return '<div class="alert alert-warning mb-0">'
                . e(__('Details konnten nicht geladen werden. Buchung: :nr', ['nr' => $booking->booking_number ?? $booking->getKey()]))
                . '</div>';
        }
    }

    protected function renderCourseDetail(CourseSession $session): string
    {
        try {
            return apply_filters('booking_reports_course_detail_render', view('plugins/courses::session-info', [
                'session' => $session,
            ])->render(), $session);
        } catch (Throwable $e) {
            Log::warning('[BookingCalendar] session-info-View für Session #' . $session->getKey() . ' fehlgeschlagen: ' . $e->getMessage());

            return '<div class="alert alert-warning mb-0">'
                . e(__('Details konnten nicht geladen werden.'))
                . '</div>';
        }
    }

    protected function safeRoute(string $name, mixed $param): ?string
    {
        try {
            return route($name, $param);
        } catch (Throwable) {
            return null;
        }
    }

    protected function safeStatusValue(Booking $booking): string
    {
        $status = $booking->status;
        if (is_object($status) && method_exists($status, 'getValue')) {
            return (string) $status->getValue();
        }

        return (string) ($status ?? 'pending');
    }

    protected function safeStatusLabel(Booking $booking): string
    {
        $status = $booking->status;
        if (is_object($status) && method_exists($status, 'label')) {
            return (string) $status->label();
        }

        return (string) ($status ?? '');
    }
}
