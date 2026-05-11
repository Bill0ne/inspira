<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Courses\Models\Course;
use Botble\Courses\Models\CourseSession;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Hotel\Http\Requests\ManualBookingRequest;
use Botble\Hotel\Models\Booking;
use Botble\Hotel\Models\BookingRoom;
use Botble\Hotel\Models\ManualBooking;
use Botble\Hotel\Models\Room;
use Botble\Hotel\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class BookingCalendarController extends BaseController
{
    public function __construct(protected AvailabilityService $availability)
    {
    }

    public function index()
    {
        $this->pageTitle(trans('plugins/hotel::booking.calendar'));

        Assets::addScriptsDirectly([
            'vendor/core/plugins/hotel/libraries/full-calendar-6.1.8/main.min.js',
            'vendor/core/plugins/hotel/js/booking-reports.js',
        ]);

        Assets::usingVueJS();

        $rooms = Room::query()->select(['id', 'name'])->orderBy('name')->get();
        $courses = Course::query()->select(['id', 'name'])->orderBy('name')->get();
        $manualBookingsEnabled = Schema::hasTable('ht_manual_bookings');

        return view('plugins/hotel::booking-calendar', compact('rooms', 'courses', 'manualBookingsEnabled'));
    }

    public function storeManual(ManualBookingRequest $request, BaseHttpResponse $response): BaseHttpResponse
    {
        if (! Schema::hasTable('ht_manual_bookings')) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/hotel::booking.manual_booking_table_missing'))
                ->setNextUrl(route('booking.calendar.index'));
        }

        $data = $request->validated();
        $start = Carbon::parse($data['start_at']);
        $end = Carbon::parse($data['end_at']);

        // Konflikt-Prüfung VOR dem Anlegen
        if ($data['type'] === 'room' && ! empty($data['room_id'])) {
            $check = $this->availability->checkRoomAvailability((int) $data['room_id'], $start, $end);
            if (! $check['available']) {
                return $response
                    ->setError()
                    ->setMessage(trans('plugins/hotel::booking.conflict.conflict_detected', [
                        'reason' => $check['reason'] ?? '',
                    ]))
                    ->setNextUrl(route('booking.calendar.index'));
            }
        }

        if ($data['type'] === 'course' && ! empty($data['course_id'])) {
            $conflicts = $this->availability->findCourseConflicts((int) $data['course_id'], $start, $end);
            if ($conflicts->isNotEmpty()) {
                return $response
                    ->setError()
                    ->setMessage(trans('plugins/hotel::booking.conflict.conflict_detected', [
                        'reason' => $conflicts->first()['label'] ?? '',
                    ]))
                    ->setNextUrl(route('booking.calendar.index'));
            }
        }

        ManualBooking::query()->create($data);

        return $response
            ->setMessage(trans('plugins/hotel::booking.manual_booking_created'))
            ->setNextUrl(route('booking.calendar.index'))
            ->setPreviousUrl(route('booking.calendar.index'));
    }

    public function kpis(Request $request): JsonResponse
    {
        // Datumsbereich vom Kalender (Vue) übernehmen, sonst aktueller Monat
        try {
            $start = $request->filled('start')
                ? Carbon::parse($request->input('start'))->startOfDay()
                : Carbon::now()->startOfMonth();

            $end = $request->filled('end')
                ? Carbon::parse($request->input('end'))->endOfDay()
                : Carbon::now()->endOfMonth();
        } catch (\Throwable) {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        }

        if ($end->lessThan($start)) {
            [$start, $end] = [$end, $start];
        }

        $totalDays = max(1, (int) $start->copy()->startOfDay()->diffInDays($end->copy()->endOfDay()) + 1);

        // --- 1. Raum-Auslastung ---
        $totalRooms = (int) (Room::query()->sum('number_of_rooms') ?: 1);
        $totalRoomDaysAvailable = $totalRooms * $totalDays;

        $bookedRoomDays = 0;
        $activeBookingRooms = BookingRoom::query()
            ->whereHas('booking', fn (Builder $q) => $q->whereNot('status', BookingStatusEnum::CANCELLED))
            ->where('start_date', '<=', $end)
            ->where('end_date', '>=', $start)
            ->get(['start_date', 'end_date', 'number_of_rooms']);

        foreach ($activeBookingRooms as $br) {
            $bStart = Carbon::parse($br->start_date)->max($start);
            $bEnd = Carbon::parse($br->end_date)->min($end);
            $days = max(1, (int) $bStart->diffInDays($bEnd) + 1);
            $bookedRoomDays += $days * ($br->number_of_rooms ?: 1);
        }

        $roomOccupancyPercent = $totalRoomDaysAvailable > 0
            ? round(($bookedRoomDays / $totalRoomDaysAvailable) * 100, 1)
            : 0;

        // --- 2. Kurs-Auslastung ---
        $courseSessions = CourseSession::query()
            ->where('start_date', '<=', $end)
            ->where('end_date', '>=', $start)
            ->withCount([
                'bookings as booked_count' => fn (Builder $q) => $q->whereIn('status', [
                    BookingStatusEnum::PENDING,
                    BookingStatusEnum::PROCESSING,
                    BookingStatusEnum::COMPLETED,
                ]),
            ])
            ->get(['id', 'available_seats']);

        $totalSeats = (int) ($courseSessions->sum('available_seats') ?: 0);
        $totalBooked = (int) $courseSessions->sum('booked_count');
        $courseOccupancyPercent = $totalSeats > 0
            ? round(($totalBooked / $totalSeats) * 100, 1)
            : 0;

        // --- 3. Überschneidungen (Kurs in einem Raum, der parallel als Zimmer gebucht ist) ---
        $overlaps = 0;
        $courseSessionsWithRoom = CourseSession::query()
            ->with('course:id,room_id')
            ->where('start_date', '<=', $end)
            ->where('end_date', '>=', $start)
            ->get(['id', 'course_id', 'start_date', 'end_date']);

        foreach ($courseSessionsWithRoom as $session) {
            $roomId = $session->course?->room_id;
            if (! $roomId) {
                continue;
            }

            $hasOverlap = BookingRoom::query()
                ->where('room_id', $roomId)
                ->whereHas('booking', fn (Builder $q) => $q->whereNot('status', BookingStatusEnum::CANCELLED))
                ->where('start_date', '<', $session->end_date)
                ->where('end_date', '>', $session->start_date)
                ->exists();

            if ($hasOverlap) {
                $overlaps++;
            }
        }

        // --- 4. Ausstehende Buchungen ---
        $pendingStatuses = [BookingStatusEnum::PENDING, BookingStatusEnum::AWAITING_PAYMENT];

        $pendingRoomBookings = Booking::query()
            ->whereIn('status', $pendingStatuses)
            ->count();

        $pendingCourseBookings = \Botble\Courses\Models\CourseBooking::query()
            ->whereIn('status', $pendingStatuses)
            ->count();

        return response()->json([
            'room_occupancy' => [
                'percent' => min($roomOccupancyPercent, 100),
                'booked' => $bookedRoomDays,
                'available' => $totalRoomDaysAvailable,
            ],
            'course_occupancy' => [
                'percent' => min($courseOccupancyPercent, 100),
                'booked' => $totalBooked,
                'available' => $totalSeats,
            ],
            'overlaps' => [
                'count' => $overlaps,
            ],
            'pending_bookings' => [
                'rooms' => $pendingRoomBookings,
                'courses' => $pendingCourseBookings,
                'total' => $pendingRoomBookings + $pendingCourseBookings,
            ],
            'range' => [
                'start' => $start->toIso8601String(),
                'end' => $end->toIso8601String(),
                'days' => $totalDays,
                'label' => $start->translatedFormat('d.m.Y') . ' – ' . $end->translatedFormat('d.m.Y'),
            ],
        ]);
    }
}
