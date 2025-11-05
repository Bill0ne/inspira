@php
    Theme::asset()->container('header')->add('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css');
    Theme::asset()->container('header')->add('flatpickr-theme-airbnb', 'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/themes/airbnb.css', ['flatpickr-css']);
    Theme::asset()->container('header')->usePath()->add('booking-widget-css', 'css/booking-widget.css');

    Theme::asset()->container('footer')->add('flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js');
    Theme::asset()->container('footer')->add('flatpickr-locale-de', 'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/de.js', ['flatpickr-js']);
    Theme::asset()->container('footer')->usePath()->add('booking-widget-js', 'js/booking-widget.js', ['flatpickr-locale-de']);
@endphp

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
        $widgetMessages = [
            'incomplete' => __('Please complete all slot fields before continuing.'),
            'duration' => __('Each slot must be at least :minutes minutes long.', ['minutes' => $minSlotDuration]),
            'overlap' => __('Slots cannot overlap.'),
            'past' => __('Slots must be scheduled in the future.'),
            'invalid' => __('Please provide a valid date and time.'),
        ];
    @endphp

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
                        >
                            <div class="booking-widget__header">
                                <h4 class="booking-widget__title">{{ __('Choose your slot') }}</h4>
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
                            <label for="adults"><i class="fal fa-users"></i>{{ __('Guests and Rooms') }}</label>
                            <button data-bb-toggle="toggle-guests-and-rooms" class="text-truncate" type="button" data-target="#toggle-guests-and-rooms">
                                <span data-bb-toggle="filter-adults-count" class="me-1">{{ BaseHelper::stringify($adults) }}</span> {{ __('Adult(s)') }} ,
                                <span data-bb-toggle="filter-children-count" class="ms-1 me-1">{{ BaseHelper::stringify($children) }}</span> {{ __('Child(ren)') }},
                                <span data-bb-toggle="filter-rooms-count" class="me-1 ms-1">{{ BaseHelper::stringify($roomsCount) }}</span> {{ __('Room(s)') }}
                            </button>

                            <div class="custom-dropdown dropdown-menu p-3" id="toggle-guests-and-rooms">
                                <div class="inputs-filed">
                                    <label for="adults">{{ __('Adults') }}</label>
                                    <div class="input-quantity">
                                        <button type="button" class="main-btn btn" data-bb-toggle="decrement-room">-</button>
                                        <input type="number" id="adults" name="adults" readonly value="{{ BaseHelper::stringify($adults) }}" min="{{ $minimumNumberOfGuests }}" max="{{ $maximumNumberOfGuests }}">
                                        <button type="button" class="main-btn btn" data-bb-toggle="increment-room">+</button>
                                    </div>
                                </div>
                                <div class="inputs-filed mt-30">
                                    <label for="children">{{ __('Children') }}</label>
                                    <div class="input-quantity">
                                        <button type="button" class="main-btn btn" data-bb-toggle="decrement-room">-</button>
                                        <input type="number" id="children" name="children" readonly value="{{ BaseHelper::stringify($children) }}" min="0" max="{{ $maximumNumberOfGuests }}">
                                        <button type="button" class="main-btn btn" data-bb-toggle="increment-room">+</button>
                                    </div>
                                </div>
                                <div class="inputs-filed mt-30">
                                    <label for="rooms">{{ __('Rooms') }}</label>
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
                            <button type="submit" class="btn ss-btn" data-animation="fadeInRight" data-delay=".8s">
                                {{ $availableForBooking ? __('Book Now') : __('Check Availability') }}
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
                >
                    @if (! empty($title))
                        <div class="booking-widget__header">
                            <h4 class="booking-widget__title">{!! BaseHelper::clean($title) !!}</h4>
                        </div>
                    @else
                        <div class="booking-widget__header">
                            <h4 class="booking-widget__title">{{ __('Choose your slot') }}</h4>
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
                                <span>{{ __('Adults') }}</span>
                            </div>
                            <div class="booking-counter__controls">
                                <button type="button" class="booking-counter__btn" data-counter-action="decrement" aria-label="{{ __('Decrease adults') }}">−</button>
                                <input
                                    type="number"
                                    class="booking-counter__input"
                                    id="booking-adults"
                                    name="adults"
                                    value="{{ BaseHelper::stringify($adults) }}"
                                    min="{{ $minimumNumberOfGuests }}"
                                    max="{{ $maximumNumberOfGuests }}"
                                >
                                <button type="button" class="booking-counter__btn" data-counter-action="increment" aria-label="{{ __('Increase adults') }}">+</button>
                            </div>
                        </div>

                        <button type="submit" class="booking-widget__submit">
                            {{ $availableForBooking ? __('Book Now') : __('Check Availability') }}
                        </button>
                    </div>
                </div>
        @endswitch
    </form>
@endif
