<?php

namespace Botble\Hotel\Services;

use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Hotel\Models\Booking;
use Botble\Hotel\Models\BookingRoom;
use Botble\Hotel\Models\ICalSyncLog;
use Botble\Hotel\Models\Room;
use Botble\Hotel\Models\RoomCalendar;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ICalService
{
    protected const ICAL_DATE_FORMAT = 'Ymd\THis\Z';
    protected const ICAL_DATE_FORMAT_DAY = 'Ymd';

    /**
     * Generate iCal file content for a room
     */
    public function generateICalContent(Room $room): string
    {
        $bookingRooms = BookingRoom::query()
            ->where('room_id', $room->id)
            ->whereHas('booking', function ($query) {
                $query->where('status', '!=', BookingStatusEnum::CANCELLED);
            })
            ->with('booking')
            ->get();

        $content = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Botble//Hotel Booking//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
        ];

        foreach ($bookingRooms as $bookingRoom) {
            $booking = $bookingRoom->booking;
            $startDate = Carbon::parse($bookingRoom->start_date);
            $endDate = Carbon::parse($bookingRoom->end_date);

            $content[] = 'BEGIN:VEVENT';
            $content[] = 'UID:' . $booking->id . '@' . parse_url(url('/'), PHP_URL_HOST);
            $content[] = 'DTSTART;VALUE=DATE:' . $startDate->format(self::ICAL_DATE_FORMAT_DAY);
            $content[] = 'DTEND;VALUE=DATE:' . $endDate->format(self::ICAL_DATE_FORMAT_DAY);
            $content[] = 'SUMMARY:' . ($booking->customer->first_name ?? '') . ' ' . ($booking->customer->last_name ?? '');
            $content[] = 'DESCRIPTION:' . $this->formatDescription($booking, $bookingRoom);
            $content[] = 'CREATED:' . Carbon::parse($booking->created_at)->format(self::ICAL_DATE_FORMAT);
            $content[] = 'LAST-MODIFIED:' . Carbon::parse($booking->updated_at)->format(self::ICAL_DATE_FORMAT);
            $content[] = 'STATUS:CONFIRMED';
            $content[] = 'END:VEVENT';
        }

        $content[] = 'END:VCALENDAR';

        return implode("\r\n", $content);
    }

    /**
     * Format booking description for iCal
     */
    protected function formatDescription(Booking $booking, BookingRoom $bookingRoom): string
    {
        $description = [
            'Booking ID: ' . $booking->id,
            'Room: ' . $bookingRoom->room_name,
            'Number of rooms: ' . $bookingRoom->number_of_rooms,
            'Check-in: ' . $bookingRoom->start_date,
            'Check-out: ' . $bookingRoom->end_date,
        ];

        if ($booking->customer) {
            $description[] = 'Customer: ' . $booking->customer->first_name . ' ' . $booking->customer->last_name;
            $description[] = 'Email: ' . $booking->customer->email;
            $description[] = 'Phone: ' . $booking->customer->phone;
        }

        return implode('\\n', $description);
    }

    /**
     * Parse iCal content and return a collection of events
     */
    public function parseICalContent(string $content): Collection
    {
        $events = collect();

        // Remove unwanted characters
        $content = preg_replace('/[\r\n]{1,} ([\w])/i', '$1', $content);

        // Split into lines
        $lines = preg_split('/[\r\n]+/i', $content);

        $inEvent = false;
        $currentEvent = [];

        foreach ($lines as $line) {
            if (strpos($line, 'BEGIN:VEVENT') !== false) {
                $inEvent = true;
                $currentEvent = [];

                continue;
            }

            if (strpos($line, 'END:VEVENT') !== false) {
                $inEvent = false;
                $events->push($currentEvent);

                continue;
            }

            if ($inEvent) {
                $parts = explode(':', $line, 2);
                if (count($parts) == 2) {
                    $key = $parts[0];
                    $value = $parts[1];

                    // Handle special cases like DTSTART;VALUE=DATE
                    if (strpos($key, ';') !== false) {
                        $keyParts = explode(';', $key);
                        $key = $keyParts[0];
                    }

                    $currentEvent[$key] = $value;
                }
            }
        }

        return $events;
    }

    /**
     * Sync external calendars for a room
     */
    public function syncExternalCalendars(Room $room): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'events' => 0,
            'errors' => [],
            'conflicts' => 0,
            'created' => 0,
            'updated' => 0,
        ];

        $calendars = $room->calendars;

        foreach ($calendars as $calendar) {
            try {
                $content = $this->fetchCalendarContent($calendar->url);

                if (! $content) {
                    $this->logSync($room->id, $calendar->id, 'error', 'Failed to fetch calendar content', [
                        'calendar_name' => $calendar->name,
                        'url' => $calendar->url,
                    ]);

                    $results['failed']++;
                    $results['errors'][] = 'Failed to fetch calendar content for ' . $calendar->name;

                    continue;
                }

                $events = $this->parseICalContent($content);
                $results['events'] += $events->count();

                $this->logSync($room->id, $calendar->id, 'success', 'Successfully fetched calendar content', [
                    'calendar_name' => $calendar->name,
                    'events_count' => $events->count(),
                ]);

                // Process events
                foreach ($events as $event) {
                    if (isset($event['DTSTART']) && isset($event['DTEND'])) {
                        $startDate = $this->parseICalDate($event['DTSTART']);
                        $endDate = $this->parseICalDate($event['DTEND']);

                        // Check for conflicts
                        $conflict = $this->checkForConflicts($room, $startDate, $endDate);

                        if ($conflict) {
                            $results['conflicts']++;
                            $this->logSync($room->id, $calendar->id, 'warning', 'Booking conflict detected', [
                                'start_date' => $startDate->format('Y-m-d'),
                                'end_date' => $endDate->format('Y-m-d'),
                                'event' => $event,
                                'conflict' => $conflict,
                            ]);
                        } else {
                            // Create a blocked date for this room
                            $created = $this->createBlockedDate($room, $startDate, $endDate, $calendar);

                            if ($created) {
                                $results['created']++;
                            } else {
                                $results['updated']++;
                            }
                        }
                    }
                }

                // Update last synced timestamp
                $calendar->last_synced_at = now();
                $calendar->save();

                $results['success']++;
            } catch (Exception $e) {
                $this->logSync($room->id, $calendar->id, 'error', 'Error syncing calendar: ' . $e->getMessage(), [
                    'calendar_name' => $calendar->name,
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                $results['failed']++;
                $results['errors'][] = 'Error syncing calendar ' . $calendar->name . ': ' . $e->getMessage();
                Log::error('iCal sync error: ' . $e->getMessage(), ['exception' => $e]);
            }
        }

        return $results;
    }

    /**
     * Fetch calendar content from URL
     */
    protected function fetchCalendarContent(string $url): ?string
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $content = curl_exec($ch);
            curl_close($ch);

            return $content ?: null;
        } catch (Exception $e) {
            Log::error('Failed to fetch iCal content: ' . $e->getMessage(), ['exception' => $e]);

            return null;
        }
    }

    /**
     * Parse iCal date format
     */
    protected function parseICalDate(string $date): Carbon
    {
        // Handle VALUE=DATE format (YYYYMMDD)
        if (strlen($date) === 8) {
            return Carbon::createFromFormat('Ymd', $date);
        }

        // Handle DATE-TIME format
        if (Str::endsWith($date, 'Z')) {
            return Carbon::createFromFormat('Ymd\THis\Z', $date);
        }

        return Carbon::createFromFormat('Ymd\THis', $date);
    }

    /**
     * Create a blocked date for a room
     *
     * @return bool True if created, false if updated
     */
    protected function createBlockedDate(Room $room, Carbon $startDate, Carbon $endDate, RoomCalendar $calendar): bool
    {
        // Create a virtual booking to block the dates
        $bookingNumber = 'ICAL-' . Str::random(6);

        // Check if we already have a booking with this number
        $existingBooking = Booking::query()
            ->where('booking_number', 'LIKE', 'ICAL-%')
            ->whereHas('rooms', function ($query) use ($room, $startDate, $endDate) {
                $query->where('room_id', $room->id)
                    ->where('start_date', $startDate->format('Y-m-d'))
                    ->where('end_date', $endDate->format('Y-m-d'));
            })
            ->first();

        if ($existingBooking) {
            // Update existing booking
            return false;
        }

        // Create a new booking
        DB::beginTransaction();

        try {
            $booking = new Booking();
            $booking->status = BookingStatusEnum::COMPLETED;
            $booking->booking_number = $bookingNumber;
            $booking->amount = 0;
            $booking->sub_total = 0;
            $booking->tax_amount = 0;
            $booking->currency_id = $room->currency_id;
            $booking->save();

            // Create booking room
            BookingRoom::query()->create([
                'room_id' => $room->id,
                'room_name' => $room->name,
                'room_image' => Arr::first($room->images),
                'booking_id' => $booking->getKey(),
                'price' => 0,
                'currency_id' => $room->currency_id,
                'number_of_rooms' => 1,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]);

            DB::commit();

            $this->logSync($room->id, $calendar->id, 'success', 'Created blocked dates', [
                'booking_number' => $bookingNumber,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]);

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            $this->logSync($room->id, $calendar->id, 'error', 'Failed to create blocked dates: ' . $e->getMessage(), [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'exception' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Check for booking conflicts
     *
     * @return array|null Conflict details or null if no conflict
     */
    protected function checkForConflicts(Room $room, Carbon $startDate, Carbon $endDate): ?array
    {
        // Check if there are any bookings for this room in the given date range
        $conflictingBookings = BookingRoom::query()
            ->where('room_id', $room->id)
            ->whereHas('booking', function ($query) {
                $query->where('status', '!=', BookingStatusEnum::CANCELLED);
            })
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->whereDate('start_date', '>=', $startDate)
                        ->whereDate('start_date', '<', $endDate);
                })
                ->orWhere(function ($q) use ($startDate, $endDate) {
                    $q->whereDate('end_date', '>', $startDate)
                        ->whereDate('end_date', '<=', $endDate);
                })
                ->orWhere(function ($q) use ($startDate, $endDate) {
                    $q->whereDate('start_date', '<=', $startDate)
                        ->whereDate('end_date', '>=', $endDate);
                });
            })
            ->where('booking_number', 'NOT LIKE', 'ICAL-%')
            ->first();

        if ($conflictingBookings) {
            return [
                'booking_id' => $conflictingBookings->booking_id,
                'booking_number' => $conflictingBookings->booking->booking_number,
                'start_date' => $conflictingBookings->start_date,
                'end_date' => $conflictingBookings->end_date,
            ];
        }

        return null;
    }

    /**
     * Log sync activity
     */
    protected function logSync(int $roomId, ?int $calendarId, string $status, string $message, array $data = []): void
    {
        ICalSyncLog::query()->create([
            'room_id' => $roomId,
            'calendar_id' => $calendarId,
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
