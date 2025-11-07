@if (is_plugin_active('hotel'))
    @php
        $minimumNumberOfGuests = HotelHelper::getMinimumNumberOfGuests();
        $maximumNumberOfGuests = HotelHelper::getMaximumNumberOfGuests();

        $adults = old('adults', request()->integer('adults', $minimumNumberOfGuests));
        $children = old('children', request()->integer('children', 0));
        $roomsCount = old('rooms', request()->integer('rooms', 1));

        $rawSlotsInput = old('slots', (array) request()->input('slots', []));
        $processedSlots = [];

        foreach ($rawSlotsInput as $value) {
            $raw = '';
            $date = '';
            $start = '';
            $end = '';

            if (is_string($value)) {
                $raw = trim($value);

                if (preg_match('/^(\d{2}\.\d{2}\.\d{4})\s+(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})$/', $raw, $matches)) {
                    $date = $matches[1];
                    $start = $matches[2];
                    $end = $matches[3];
                }
            } elseif (is_array($value)) {
                $date = trim((string) ($value['date'] ?? ''));
                $start = trim((string) ($value['start'] ?? ($value['start_time'] ?? '')));
                $end = trim((string) ($value['end'] ?? ($value['end_time'] ?? '')));

                $startDateValue = $value['start_date'] ?? null;
                $endDateValue = $value['end_date'] ?? null;

                if ($startDateValue) {
                    try {
                        $startCarbon = Carbon\Carbon::parse($startDateValue);
                        $date = $date ?: $startCarbon->format('d.m.Y');
                        $start = $start ?: $startCarbon->format('H:i');
                    } catch (\Throwable $exception) {
                    }
                }

                if ($endDateValue) {
                    try {
                        $endCarbon = Carbon\Carbon::parse($endDateValue);
                        $date = $date ?: $endCarbon->format('d.m.Y');
                        $end = $end ?: $endCarbon->format('H:i');
                    } catch (\Throwable $exception) {
                    }
                }

                if ($date && $start && $end) {
                    $raw = sprintf('%s %s - %s', $date, $start, $end);
                }
            }

            $processedSlots[] = [
                'date' => $date,
                'start' => $start,
                'end' => $end,
                'raw' => $raw,
            ];
        }

        if (empty($processedSlots)) {
            $processedSlots[] = [
                'date' => '',
                'start' => '',
                'end' => '',
                'raw' => '',
            ];
        }

        $minSlotDuration = 30;
        $widgetId = uniqid('booking-widget-');
        $operatingHours = [
            'start' => '09:30',
            'end' => '22:30',
        ];

        $roomMaxAdults = null;
        $roomMaxChildren = null;
        $maxParticipants = null;
        $existingBookings = [];
        $courseSessions = [];

        if (isset($room) && $room instanceof \Botble\Hotel\Models\Room) {
            $roomMaxAdults = $room->max_adults ?: null;
            $roomMaxChildren = $room->max_children ?: null;

            if ($roomMaxAdults !== null || $roomMaxChildren !== null) {
                $maxParticipants = max(0, (int) ($roomMaxAdults ?? 0)) + max(0, (int) ($roomMaxChildren ?? 0));

                if ($maxParticipants === 0) {
                    $maxParticipants = null;
                }
            }

            $bookings = $room->activeBookingRooms()
                ->select(['ht_booking_rooms.start_date', 'ht_booking_rooms.end_date'])
                ->get();

            foreach ($bookings as $booking) {
                if (! $booking->start_date || ! $booking->end_date) {
                    continue;
                }

                $existingBookings[] = [
                    'start' => $booking->start_date->format('Y-m-d H:i'),
                    'end' => $booking->end_date->format('Y-m-d H:i'),
                ];
            }

            if (is_plugin_active('courses')) {
                $courses = \Botble\Courses\Models\Course::query()
                    ->where('room_id', $room->getKey())
                    ->with(['sessions' => function ($query) {
                        $query->select(['id', 'course_id', 'start_date', 'end_date']);
                    }])
                    ->get();

                foreach ($courses as $course) {
                    foreach ($course->sessions as $session) {
                        if (! $session->start_date || ! $session->end_date) {
                            continue;
                        }

                        $courseSessions[] = [
                            'start' => $session->start_date->format('Y-m-d H:i'),
                            'end' => $session->end_date->format('Y-m-d H:i'),
                            'title' => $course->name,
                        ];
                    }
                }
            }
        }

        $widgetMessages = [
            'incomplete' => 'Bitte füllen Sie alle Slot-Felder aus.',
            'duration' => 'Jeder Slot muss mindestens :minutes Minuten umfassen.',
            'overlap' => 'Slots dürfen sich nicht überschneiden.',
            'past' => 'Slots müssen in der Zukunft liegen.',
            'invalid' => 'Bitte geben Sie ein gültiges Datum und eine gültige Uhrzeit ein.',
            'course' => 'In diesem Zeitraum findet bereits ein Kurs statt.',
            'booked' => 'Der Raum ist in diesem Zeitraum bereits reserviert.',
            'capacity' => 'Die Anzahl der Erwachsenen überschreitet die maximale Kapazität.',
            'participants' => 'Die Gesamtzahl der Teilnehmenden überschreitet die maximale Kapazität.',
            'hours' => 'Buchungen sind nur zwischen :start und :end Uhr möglich.',
            'ready' => 'Alle Angaben sehen gut aus. Sie können jetzt buchen.',
            'initial' => 'Bitte wählen Sie Datum und Uhrzeit für Ihre Buchung.',
        ];

        $encodeForAttribute = static function ($value) {
            $encoded = json_encode($value, JSON_UNESCAPED_UNICODE);

            if ($encoded === false) {
                $encoded = '[]';
            }

            return e($encoded);
        };
    @endphp

    {{-- === Litepicker Styles & Script === --}}
    <link rel="stylesheet" href="/themes/riorelax/css/booking-widget.css">
    <link rel="stylesheet" href="/themes/riorelax/css/litepicker.css">
    <script src="/themes/riorelax/js/litepicker.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const attachPicker = (el, options) => {
                if (!el || el.dataset.litepickerBound === 'true' || typeof Litepicker === 'undefined') {
                    return;
                }

                el.dataset.litepickerBound = 'true';

                new Litepicker({
                    element: el,
                    singleMode: true,
                    autoApply: true,
                    format: options.format,
                    lang: 'de-DE',
                    showTime: options.showTime || false,
                    showSeconds: false,
                    dropdowns: options.dropdowns || undefined,
                    tooltipText: {
                        one: 'Tag',
                        other: 'Tage'
                    }
                });
            };

            const hydrateWidget = (widget) => {
                if (!widget) {
                    return;
                }

                widget.querySelectorAll('[data-role="slot-date"]').forEach((input) => {
                    attachPicker(input, { format: 'DD.MM.YYYY' });
                });

                widget.querySelectorAll('[data-role="slot-start"], [data-role="slot-end"]').forEach((input) => {
                    attachPicker(input, { format: 'HH:mm', showTime: true, dropdowns: { minutes: true, hours: true } });
                });
            };

            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    mutation.addedNodes.forEach((node) => {
                        if (!(node instanceof HTMLElement)) {
                            return;
                        }

                        if (node.matches('[data-booking-widget]')) {
                            hydrateWidget(node);
                        }

                        const ownerWidget = node.closest('[data-booking-widget]');
                        if (ownerWidget) {
                            hydrateWidget(ownerWidget);
                        }

                        node.querySelectorAll('[data-booking-widget]').forEach((widget) => {
                            hydrateWidget(widget);
                        });
                    });
                });
            });

            document.querySelectorAll('[data-booking-widget]').forEach((widget) => {
                hydrateWidget(widget);
                observer.observe(widget, { childList: true, subtree: true });
            });
        });
    </script>
    <script defer src="/themes/riorelax/js/booking-widget.js"></script>

    <form action="{{ $availableForBooking ? route('public.booking') : route('public.rooms') }}" method="{{ $availableForBooking ? 'POST' : 'GET' }}" class="contact-form mt-30 form-booking">
        @if ($availableForBooking)
            @csrf
            <input type="hidden" name="room_id" value="{{ $room->id }}">
        @endif

        @switch($style)
            @case(2)
                <div class="row align-items-start g-4 booking-form booking-form--style-2">
                    @if (! empty($title))
                        <div class="col-lg-12">
                            <div class="section-title center-align mb-3">
                                <h2>{!! BaseHelper::clean($title) !!}</h2>
                            </div>
                        </div>
                    @endif

                    <div class="col-lg-5 col-md-6">
                        <div
                            class="booking-widget booking-widget--fluid"
                            data-booking-widget
                            data-widget-id="{{ $widgetId }}"
                            data-min-duration="{{ $minSlotDuration }}"
                            data-error-incomplete="{{ $widgetMessages['incomplete'] }}"
                            data-error-duration="{{ $widgetMessages['duration'] }}"
                            data-error-overlap="{{ $widgetMessages['overlap'] }}"
                            data-error-past="{{ $widgetMessages['past'] }}"
                            data-error-invalid="{{ $widgetMessages['invalid'] }}"
                            data-error-course="{{ $widgetMessages['course'] }}"
                            data-error-booked="{{ $widgetMessages['booked'] }}"
                            data-error-capacity="{{ $widgetMessages['capacity'] }}"
                            data-error-participants="{{ $widgetMessages['participants'] }}"
                            data-error-hours="{{ str_replace([':start', ':end'], [$operatingHours['start'], $operatingHours['end']], $widgetMessages['hours']) }}"
                            data-status-ready="{{ $widgetMessages['ready'] }}"
                            data-status-initial="{{ $widgetMessages['initial'] }}"
                            data-booked-slots="{{ $encodeForAttribute($existingBookings) }}"
                            data-course-sessions="{{ $encodeForAttribute($courseSessions) }}"
                            data-operating-hours="{{ $encodeForAttribute($operatingHours) }}"
                            data-max-adults="{{ $roomMaxAdults ?? '' }}"
                            data-max-participants="{{ $maxParticipants ?? '' }}"
                        >
                            <div class="booking-widget__header">
                                <h4 class="booking-widget__title">Wunschtermin auswählen</h4>
                            </div>

                            @include(Theme::getThemeNamespace('partials.hotel.forms.booking-slots'), [
                                'slots' => $processedSlots,
                                'widgetId' => $widgetId,
                                'minDuration' => $minSlotDuration,
                            ])
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="contact-field p-relative c-name form-guests-and-rooms-wrapper">
                            <label for="adults"><i class="fal fa-users"></i>Gäste &amp; Räume</label>
                            <button data-bb-toggle="toggle-guests-and-rooms" class="text-truncate" type="button" data-target="#toggle-guests-and-rooms">
                                <span data-bb-toggle="filter-adults-count" class="me-1">{{ BaseHelper::stringify($adults) }}</span> Erwachsene,
                                <span data-bb-toggle="filter-children-count" class="ms-1 me-1">{{ BaseHelper::stringify($children) }}</span> Kinder,
                                <span data-bb-toggle="filter-rooms-count" class="me-1 ms-1">{{ BaseHelper::stringify($roomsCount) }}</span> Räume
                            </button>

                            <div class="custom-dropdown dropdown-menu p-3" id="toggle-guests-and-rooms">
                                <div class="inputs-filed">
                                    <label for="adults">Erwachsene</label>
                                    <div class="input-quantity">
                                        <button type="button" class="main-btn btn" data-bb-toggle="decrement-room">-</button>
                                        <input type="number" id="adults" name="adults" readonly value="{{ BaseHelper::stringify($adults) }}" min="{{ $minimumNumberOfGuests }}" max="{{ $maximumNumberOfGuests }}">
                                        <button type="button" class="main-btn btn" data-bb-toggle="increment-room">+</button>
                                    </div>
                                </div>
                                <div class="inputs-filed mt-30">
                                    <label for="children">Kinder</label>
                                    <div class="input-quantity">
                                        <button type="button" class="main-btn btn" data-bb-toggle="decrement-room">-</button>
                                        <input type="number" id="children" name="children" readonly value="{{ BaseHelper::stringify($children) }}" min="0" max="{{ $maximumNumberOfGuests }}">
                                        <button type="button" class="main-btn btn" data-bb-toggle="increment-room">+</button>
                                    </div>
                                </div>
                                <div class="inputs-filed mt-30">
                                    <label for="rooms">Räume</label>
                                    <div class="input-quantity">
                                        <button type="button" class="main-btn btn" data-bb-toggle="decrement-room">-</button>
                                        <input type="number" id="rooms" name="rooms" readonly value="{{ BaseHelper::stringify($roomsCount) }}" min="1" max="10">
                                        <button type="button" class="main-btn btn" data-bb-toggle="increment-room">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="slider-btn">
                            <div
                                class="booking-widget__status booking-widget__status--info"
                                data-status
                                data-status-for="{{ $widgetId }}"
                                data-status-level="info"
                                aria-live="polite"
                            >
                                <span class="booking-widget__status-indicator" aria-hidden="true"></span>
                                <p class="booking-widget__status-text" data-status-message>{{ $widgetMessages['initial'] }}</p>
                            </div>

                            <button
                                type="submit"
                                class="btn ss-btn"
                                data-animation="fadeInRight"
                                data-delay=".8s"
                                data-submit
                                aria-disabled="true"
                                disabled
                            >
                                {{ $availableForBooking ? 'Jetzt buchen' : 'Verfügbarkeit prüfen' }}
                            </button>
                        </div>
                    </div>
                </div>
                @break

            @default
                <div
                    class="booking-widget"
                    data-booking-widget
                    data-widget-id="{{ $widgetId }}"
                    data-min-duration="{{ $minSlotDuration }}"
                    data-error-incomplete="{{ $widgetMessages['incomplete'] }}"
                    data-error-duration="{{ $widgetMessages['duration'] }}"
                    data-error-overlap="{{ $widgetMessages['overlap'] }}"
                    data-error-past="{{ $widgetMessages['past'] }}"
                    data-error-invalid="{{ $widgetMessages['invalid'] }}"
                    data-error-course="{{ $widgetMessages['course'] }}"
                    data-error-booked="{{ $widgetMessages['booked'] }}"
                    data-error-capacity="{{ $widgetMessages['capacity'] }}"
                    data-error-participants="{{ $widgetMessages['participants'] }}"
                    data-error-hours="{{ str_replace([':start', ':end'], [$operatingHours['start'], $operatingHours['end']], $widgetMessages['hours']) }}"
                    data-status-ready="{{ $widgetMessages['ready'] }}"
                    data-status-initial="{{ $widgetMessages['initial'] }}"
                    data-booked-slots="{{ $encodeForAttribute($existingBookings) }}"
                    data-course-sessions="{{ $encodeForAttribute($courseSessions) }}"
                    data-operating-hours="{{ $encodeForAttribute($operatingHours) }}"
                    data-max-adults="{{ $roomMaxAdults ?? '' }}"
                    data-max-participants="{{ $maxParticipants ?? '' }}"
                >
                    @if (! empty($title))
                        <div class="booking-widget__header">
                            <h4 class="booking-widget__title">{!! BaseHelper::clean($title) !!}</h4>
                        </div>
                    @else
                        <div class="booking-widget__header">
                            <h4 class="booking-widget__title">Wunschtermin auswählen</h4>
                        </div>
                    @endif

                    @include(Theme::getThemeNamespace('partials.hotel.forms.booking-slots'), [
                        'slots' => $processedSlots,
                        'widgetId' => $widgetId,
                        'minDuration' => $minSlotDuration,
                    ])

                    <div class="booking-widget__footer">
                        <div class="booking-counter" data-counter>
                            <div class="booking-counter__label">
                                <i class="fal fa-users" aria-hidden="true"></i>
                                <span>Erwachsene</span>
                            </div>
                            <div class="booking-counter__controls">
                                <button type="button" class="booking-counter__btn" data-counter-action="decrement" aria-label="Weniger Erwachsene">−</button>
                                <input
                                    type="number"
                                    class="booking-counter__input"
                                    id="booking-adults"
                                    name="adults"
                                    value="{{ BaseHelper::stringify($adults) }}"
                                    min="{{ $minimumNumberOfGuests }}"
                                    max="{{ $maximumNumberOfGuests }}"
                                >
                                <button type="button" class="booking-counter__btn" data-counter-action="increment" aria-label="Mehr Erwachsene">+</button>
                            </div>
                        </div>

                        <div
                            class="booking-widget__status booking-widget__status--info"
                            data-status
                            data-status-for="{{ $widgetId }}"
                            data-status-level="info"
                            aria-live="polite"
                        >
                            <span class="booking-widget__status-indicator" aria-hidden="true"></span>
                            <p class="booking-widget__status-text" data-status-message>{{ $widgetMessages['initial'] }}</p>
                        </div>

                        <button type="submit" class="booking-widget__submit" data-submit aria-disabled="true" disabled>
                            {{ $availableForBooking ? 'Jetzt buchen' : 'Verfügbarkeit prüfen' }}
                        </button>
                    </div>
                </div>
        @endswitch
    </form>
@endif
