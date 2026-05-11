<?php

namespace Botble\Hotel\Services;

use Botble\Courses\Models\Course;
use Botble\Courses\Models\CourseSession;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Hotel\Models\BookingRoom;
use Botble\Hotel\Models\ManualBooking;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Zentrale Verfügbarkeitsprüfung für Räume und Kurssessions.
 *
 * Berücksichtigt:
 *  - aktive Raumbuchungen (BookingRoom, Status != cancelled)
 *  - Kurssessions, die in dem Raum stattfinden (Course->room_id)
 *  - manuelle Sperren (ManualBooking) auf Raum oder Kurs
 *
 * Wird genutzt von:
 *  - Buchungsformular Räume (PublicController@postBooking)
 *  - Kursbuchung (Courses\PublicController)
 *  - Buchungskalender (manuelle Buchung)
 */
class AvailabilityService
{
    /**
     * Prüft, ob ein Raum im gegebenen Zeitraum für die angeforderte Menge buchbar ist.
     *
     * @return array{available: bool, conflicts: Collection, reason: ?string}
     */
    public function checkRoomAvailability(
        int $roomId,
        Carbon $start,
        Carbon $end,
        int $quantity = 1,
        ?int $ignoreBookingId = null
    ): array {
        $conflicts = $this->findRoomConflicts($roomId, $start, $end, $ignoreBookingId);

        if ($conflicts->isEmpty()) {
            return [
                'available' => true,
                'conflicts' => $conflicts,
                'reason' => null,
            ];
        }

        $first = $conflicts->first();

        return [
            'available' => false,
            'conflicts' => $conflicts,
            'reason' => $first['label'] ?? null,
        ];
    }

    /**
     * Liefert alle Konflikte für einen Raum im gegebenen Zeitraum.
     *
     * @return Collection<int, array{type: string, label: string, start: Carbon, end: Carbon, source_id: int}>
     */
    public function findRoomConflicts(
        int $roomId,
        Carbon $start,
        Carbon $end,
        ?int $ignoreBookingId = null
    ): Collection {
        $conflicts = collect();

        // 1. Bestehende Zimmerbuchungen
        $bookingRoomsQuery = BookingRoom::query()
            ->where('room_id', $roomId)
            ->whereHas('booking', fn (Builder $q) => $q->whereNot('status', BookingStatusEnum::CANCELLED))
            ->where('start_date', '<', $end)
            ->where('end_date', '>', $start);

        if ($ignoreBookingId) {
            $bookingRoomsQuery->where('booking_id', '!=', $ignoreBookingId);
        }

        foreach ($bookingRoomsQuery->get() as $row) {
            $conflicts->push([
                'type' => 'booking',
                'label' => trans('plugins/hotel::booking.conflict.room_booked', [
                    'start' => Carbon::parse($row->start_date)->format('d.m.Y H:i'),
                    'end' => Carbon::parse($row->end_date)->format('d.m.Y H:i'),
                ]),
                'start' => Carbon::parse($row->start_date),
                'end' => Carbon::parse($row->end_date),
                'source_id' => (int) $row->booking_id,
            ]);
        }

        // 2. Kurssessions, die diesen Raum belegen
        $courseSessions = CourseSession::query()
            ->whereHas('course', fn (Builder $q) => $q->where('room_id', $roomId))
            ->where('start_date', '<', $end)
            ->where('end_date', '>', $start)
            ->with('course:id,name,room_id')
            ->get();

        foreach ($courseSessions as $session) {
            $conflicts->push([
                'type' => 'course_session',
                'label' => trans('plugins/hotel::booking.conflict.course_session', [
                    'course' => $session->course?->name ?? ('#' . $session->course_id),
                    'start' => Carbon::parse($session->start_date)->format('d.m.Y H:i'),
                    'end' => Carbon::parse($session->end_date)->format('d.m.Y H:i'),
                ]),
                'start' => Carbon::parse($session->start_date),
                'end' => Carbon::parse($session->end_date),
                'source_id' => (int) $session->id,
            ]);
        }

        // 3. Manuelle Sperren (defensiv: Tabelle könnte fehlen)
        if (Schema::hasTable('ht_manual_bookings')) {
            $manual = ManualBooking::query()
                ->where('type', 'room')
                ->where('room_id', $roomId)
                ->where('start_at', '<', $end)
                ->where('end_at', '>', $start)
                ->get();

            foreach ($manual as $block) {
                $conflicts->push([
                    'type' => 'manual',
                    'label' => trans('plugins/hotel::booking.conflict.manual_block', [
                        'start' => Carbon::parse($block->start_at)->format('d.m.Y H:i'),
                        'end' => Carbon::parse($block->end_at)->format('d.m.Y H:i'),
                    ]),
                    'start' => Carbon::parse($block->start_at),
                    'end' => Carbon::parse($block->end_at),
                    'source_id' => (int) $block->id,
                ]);
            }
        }

        return $conflicts->values();
    }

    /**
     * Prüft, ob eine Kurssession buchbar ist (freie Plätze + Raum frei).
     *
     * @return array{available: bool, reason: ?string}
     */
    public function checkCourseSessionAvailability(CourseSession $session): array
    {
        // 1. Freie Plätze
        if (method_exists($session, 'hasAvailableSeats') && ! $session->hasAvailableSeats()) {
            return [
                'available' => false,
                'reason' => trans('plugins/hotel::booking.conflict.course_full'),
            ];
        }

        // 2. Raum-Konflikte (falls Kurs einem Raum zugeordnet ist)
        $roomId = $session->course?->room_id;

        if ($roomId) {
            $start = Carbon::parse($session->start_date);
            $end = Carbon::parse($session->end_date);

            // Manuelle Raum-Sperren in diesem Zeitraum verhindern auch die Kursbuchung
            if (Schema::hasTable('ht_manual_bookings')) {
                $hasManualBlock = ManualBooking::query()
                    ->where('type', 'room')
                    ->where('room_id', $roomId)
                    ->where('start_at', '<', $end)
                    ->where('end_at', '>', $start)
                    ->exists();

                if ($hasManualBlock) {
                    return [
                        'available' => false,
                        'reason' => trans('plugins/hotel::booking.conflict.course_room_blocked'),
                    ];
                }

                $hasCourseBlock = ManualBooking::query()
                    ->where('type', 'course')
                    ->where('course_id', $session->course_id)
                    ->where('start_at', '<', $end)
                    ->where('end_at', '>', $start)
                    ->exists();

                if ($hasCourseBlock) {
                    return [
                        'available' => false,
                        'reason' => trans('plugins/hotel::booking.conflict.course_blocked'),
                    ];
                }
            }
        }

        return ['available' => true, 'reason' => null];
    }

    /**
     * Prüft, ob ein Kurs (gesamt) im Zeitraum gesperrt ist.
     */
    public function findCourseConflicts(int $courseId, Carbon $start, Carbon $end): Collection
    {
        if (! Schema::hasTable('ht_manual_bookings')) {
            return collect();
        }

        return ManualBooking::query()
            ->where('type', 'course')
            ->where('course_id', $courseId)
            ->where('start_at', '<', $end)
            ->where('end_at', '>', $start)
            ->get()
            ->map(fn (ManualBooking $b) => [
                'type' => 'manual',
                'label' => trans('plugins/hotel::booking.conflict.manual_block', [
                    'start' => Carbon::parse($b->start_at)->format('d.m.Y H:i'),
                    'end' => Carbon::parse($b->end_at)->format('d.m.Y H:i'),
                ]),
                'start' => Carbon::parse($b->start_at),
                'end' => Carbon::parse($b->end_at),
                'source_id' => (int) $b->id,
            ]);
    }
}
