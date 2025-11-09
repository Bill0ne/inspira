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
                    } catch (\Throwable $exception) {}
                }

                if ($endDateValue) {
                    try {
                        $endCarbon = Carbon\Carbon::parse($endDateValue);
                        $date = $date ?: $endCarbon->format('d.m.Y');
                        $end = $end ?: $endCarbon->format('H:i');
                    } catch (\Throwable $exception) {}
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
        $widgetMessages = [
            'incomplete' => __('Bitte alle Felder ausfüllen.'),
            'duration' => __('Jeder Slot muss mindestens :minutes Minuten dauern.', ['minutes' => $minSlotDuration]),
            'overlap' => __('Slots dürfen sich nicht überschneiden.'),
            'past' => __('Slots müssen in der Zukunft liegen.'),
            'invalid' => __('Bitte gültiges Datum und Zeit auswählen.'),
        ];
    @endphp

    {{-- === Assets === --}}
    <link rel="stylesheet" href="/themes/riorelax/css/booking-widget.css">
    <link rel="stylesheet" href="/themes/riorelax/css/litepicker.css">
    <link rel="stylesheet" href="/themes/riorelax/css/booking-widget-info.css">

    <script src="/themes/riorelax/js/litepicker.js"></script>
    <script defer src="/themes/riorelax/js/booking-widget.js"></script>
    <script defer src="/themes/riorelax/js/booking-widget-info.js"></script>

    {{-- === Date + Time Picker Init === --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const attachPicker = (el, options) => {
                if (!el || el.dataset.litepickerBound === 'true' || typeof Litepicker === 'undefined') return;

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
                    tooltipText: { one: 'Tag', other: 'Tage' }
                });
            };

            const hydrateWidget = (widget) => {
                if (!widget) return;
                widget.querySelectorAll('[data-role="slot-date"]').forEach((input) => attachPicker(input, { format: 'DD.MM.YYYY' }));
                widget.querySelectorAll('[data-role="slot-start"], [data-role="slot-end"]').forEach((input) =>
                    attachPicker(input, { format: 'HH:mm', showTime: true, dropdowns: { minutes: true, hours: true } })
                );
            };

            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    mutation.addedNodes.forEach((node) => {
                        if (!(node instanceof HTMLElement)) return;
                        if (node.matches('[data-booking-widget]')) hydrateWidget(node);
                        const ownerWidget = node.closest('[data-booking-widget]');
                        if (ownerWidget) hydrateWidget(ownerWidget);
                        node.querySelectorAll('[data-booking-widget]').forEach((widget) => hydrateWidget(widget));
                    });
                });
            });

            document.querySelectorAll('[data-booking-widget]').forEach((widget) => {
                hydrateWidget(widget);
                observer.observe(widget, { childList: true, subtree: true });
            });
        });
    </script>

    {{-- === FORMULAR === --}}
    <form action="{{ $availableForBooking ? route('public.booking') : route('public.rooms') }}"
          method="{{ $availableForBooking ? 'POST' : 'GET' }}"
          class="contact-form mt-30 form-booking">

        @if ($availableForBooking)
            @csrf
            <input type="hidden" name="room_id" value="{{ $room->id }}">
        @endif

        <div class="booking-widget" 
             data-booking-widget
             data-widget-id="{{ $widgetId }}"
             data-min-duration="{{ $minSlotDuration }}"
             data-error-incomplete="{{ $widgetMessages['incomplete'] }}"
             data-error-duration="{{ $widgetMessages['duration'] }}"
             data-error-overlap="{{ $widgetMessages['overlap'] }}"
             data-error-past="{{ $widgetMessages['past'] }}"
             data-error-invalid="{{ $widgetMessages['invalid'] }}"
        >
            <div class="booking-widget__header">
                <h4 class="booking-widget__title">Wähle deinen Zeitraum</h4>
            </div>

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
                        <input type="number"
                               class="booking-counter__input"
                               id="booking-adults"
                               name="adults"
                               value="{{ BaseHelper::stringify($adults) }}"
                               min="{{ $minimumNumberOfGuests }}"
                               max="{{ $maximumNumberOfGuests }}">
                        <button type="button" class="booking-counter__btn" data-counter-action="increment" aria-label="Mehr Erwachsene">+</button>
                    </div>
                </div>

                {{-- Info-Box wird automatisch per JS vor dem Button eingefügt --}}
                <button type="submit" class="booking-widget__submit">Jetzt buchen</button>
            </div>
        </div>
    </form>
@endif
