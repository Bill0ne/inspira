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
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingCalendarController extends BaseController
{
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

        return view('plugins/hotel::booking-calendar', compact('rooms', 'courses'));
    }

    public function storeManual(ManualBookingRequest $request, BaseHttpResponse $response): BaseHttpResponse
    {
        ManualBooking::query()->create($request->validated());

        return $response
            ->setMessage(trans('plugins/hotel::booking.manual_booking_created'))
            ->setNextUrl(route('booking.calendar.index'))
            ->setPreviousUrl(route('booking.calendar.index'));
    }

    public function kpis(Request $request): JsonResponse
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;

        // --- 1. Raum-Auslastung (Monat) ---
        $totalRooms = Room::query()->sum('number_of_rooms') ?: 1;
        $totalRoomDaysAvailable = $totalRooms * $daysInMonth;

        $bookedRoomDays = 0;
        $activeBookingRooms = BookingRoom::query()
            ->whereHas('booking', fn (Builder $q) => $q->whereNot('status', BookingStatusEnum::CANCELLED))
            ->where('start_date', '<=', $endOfMonth)
            ->where('end_date', '>=', $startOfMonth)
            ->get(['start_date', 'end_date', 'number_of_rooms']);

        foreach ($activeBookingRooms as $br) {
            $start = Carbon::parse($br->start_date)->max($startOfMonth);
            $end = Carbon::parse($br->end_date)->min($endOfMonth);
            $days = $start->diffInDays($end) + 1;
            $bookedRoomDays += $days * ($br->number_of_rooms ?: 1);
        }

        $roomOccupancyPercent = $totalRoomDaysAvailable > 0
            ? round(($bookedRoomDays / $totalRoomDaysAvailable) * 100, 1)
            : 0;

        // --- 2. Kurs-Auslastung (Monat) ---
        $courseSessions = CourseSession::query()
            ->where('start_date', '<=', $endOfMonth)
            ->where('end_date', '>=', $startOfMonth)
            ->withCount([
                'bookings as booked_count' => fn (Builder $q) => $q->whereIn('status', [
                    BookingStatusEnum::PENDING,
                    BookingStatusEnum::PROCESSING,
                    BookingStatusEnum::COMPLETED,
                ]),
            ])
            ->get(['id', 'available_seats']);

        $totalSeats = $courseSessions->sum('available_seats') ?: 1;
        $totalBooked = $courseSessions->sum('booked_count');
        $courseOccupancyPercent = round(($totalBooked / $totalSeats) * 100, 1);

        // --- 3. Überschneidungen (Kurs + Raum am selben Raum/Tag) ---
        $overlaps = 0;

        $courseSessionsWithRoom = CourseSession::query()
            ->with('course:id,room_id')
            ->where('start_date', '<=', $endOfMonth)
            ->where('end_date', '>=', $startOfMonth)
            ->get(['id', 'course_id', 'start_date', 'end_date']);

        foreach ($courseSessionsWithRoom as $session) {
            $roomId = $session->course?->room_id;
            if (! $roomId) {
                continue;
            }

            $hasOverlap = BookingRoom::query()
                ->whereHas('booking', fn (Builder $q) => $q->whereNot('status', BookingStatusEnum::CANCELLED))
                ->whereHas('room', fn (Builder $q) => $q->where('id', $roomId))
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
            'month' => $startOfMonth->translatedFormat('F Y'),
        ]);
    }
}
