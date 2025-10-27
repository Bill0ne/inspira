@php
    Theme::asset()->container('header')->usePath()->add('theme-css', 'css/theme.css');
        Theme::asset()->container('footer')->add('popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js', ['jquery']);
        Theme::asset()->container('footer')->add('bootstrap-js', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.bundle.min.js', ['jquery', 'popper']);
      Theme::asset()->container('footer')->usePath()->add('moment-js', 'vendors/moment.min.js');
      Theme::asset()->container('footer')->usePath()->add('date-picker', 'vendors/date-picker.min.js');
        Theme::asset()->container('footer')->usePath()->add('datetime-js', 'js/datetime.js');

@endphp

<style>
    .booking-slots-wrapper .slot-item {
        position: relative;
        margin-bottom: 10px;
        transition: background 0.2s ease;
    }

    .booking-slots-wrapper .slot-item.new-slot {
        border: 1px solid #e5e7eb;
        padding: 10px;
        background: #f9fafb;
        border-radius: 8px;
    }

    .booking-slots-wrapper .slot-item .remove-slot-btn {
        position: absolute;
        top: 8px;
        right: 10px;
        width: 22px;
        height: 22px;
        border: 1px solid #000;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        cursor: pointer;
        background: #fff;
        border-radius: 3px;
        color: #000;
        font-size: 14px;
        line-height: 1;
        transition: 0.2s ease;
    }

    .booking-slots-wrapper .slot-item .remove-slot-btn:hover {
        background: #000;
        color: #fff;
    }

</style>

@if (is_plugin_active('hotel'))
    @php
        $minimumNumberOfGuests = HotelHelper::getMinimumNumberOfGuests();
        $maximumNumberOfGuests = HotelHelper::getMaximumNumberOfGuests();
        $startDate = request()->query('start_date', Carbon\Carbon::now()->format(HotelHelper::getDateFormat()));
        $endDate = request()->query('end_date', Carbon\Carbon::now()->addDay()->format(HotelHelper::getDateFormat()));
        $adults = request()->query('adults', $minimumNumberOfGuests);
    @endphp

    <form action="{{ $availableForBooking ? route('public.booking') : route('public.rooms') }}" method="{{ $availableForBooking ? 'POST' : 'GET' }}" class="contact-form mt-30 form-booking">
        @if ($availableForBooking)
            @csrf
            <input type="hidden" name="room_id" value="{{ $room->id }}">
        @endif

        @switch($style)
            @case(2)
                <div class="row align-items-center">
                    @if (! empty($title))
                        <div class="col-lg-12">
                            <div class="section-title center-align mb-30">
                                <h2>{!! BaseHelper::clean($title) !!}</h2>
                            </div>
                        </div>
                    @endif
                        <div id="booking-slots" class="booking-slots-wrapper">
                            <div class="slot-item0 slot-item">
                                <div class="row">
                    {{-- Check In (ID FIXED: StartDateTimePicker) --}}
                    <div class="col-lg-2 col-md-6 mb-30">
                        <div class="contact-field p-relative c-name">
                            <label for="availability-form-start-date"><i class="fal fa-badge-check"></i>{{ __('Check In Date') }}</label>
                            <div class="input-group date" id="StartDateTimePicker" data-target-input="nearest">
                                <input
                                    id="availability-form-start-date"
                                    autocomplete="off"
                                    type="text"
                                    class="theme-date-input-start"
                                    data-target="#StartDateTimePicker"
                                    data-date-format="{{ HotelHelper::getBookingFormDateFormat() }}"
                                    placeholder="DD.MM.YYYY HH:mm"
                                    data-locale="{{ App::getLocale() }}"
                                    value="{{ BaseHelper::stringify($availableForBooking ? old('start_date', $startDate) : $startDate) }}"
                                    name="slots[0][start_date]"
                                >
                                <span class="input-group-text" data-target="#StartDateTimePicker" data-toggle="datetimepicker">
                                    <i class="fal fa-calendar-alt"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Check Out (ID FIXED: EndDateTimePicker) --}}
                    <div class="col-lg-2 col-md-6 mb-30">
                        <div class="contact-field p-relative c-name">
                            <label for="availability-form-end-date"><i class="fal fa-times-octagon"></i>{{ __('Check Out Date') }}</label>
                            <div class="input-group date" id="EndDateTimePicker" data-target-input="nearest">
                                <input
                                    type="text"
                                    id="availability-form-end-date"
                                    autocomplete="off"
                                    class="theme-date-input-start"
                                    data-target="#EndDateTimePicker"
                                    data-date-format="{{ HotelHelper::getBookingFormDateFormat() }}"
                                    placeholder="DD.MM.YYYY HH:mm"
                                    data-locale="{{ App::getLocale() }}"
                                    value="{{ BaseHelper::clean($availableForBooking ? old('end_date', $endDate) : $endDate) }}"
                                    name="slots[0][end_date]"
                                >
                                <span class="input-group-text" data-target="#EndDateTimePicker" data-toggle="datetimepicker">
                                    <i class="fal fa-calendar-alt"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <button type="button" id="add-slot" class="btn btn-add-slot">
                                + {{ __('Add New') }}
                            </button>
                        </div>
                    {{-- Guests / Rooms (dein Block) --}}
                    <div class="col-lg-5 col-md-6 mb-30">
                        <div class="contact-field p-relative c-name form-guests-and-rooms-wrapper">
                            <label for="adults"><i class="fal fa-users"></i>{{ __('Guests and Rooms') }}</label>
                            <button data-bb-toggle="toggle-guests-and-rooms" class="text-truncate" type="button" data-target="#toggle-guests-and-rooms">
                                <span data-bb-toggle="filter-adults-count" class="me-1">1</span> {{ __('Adult(s)') }} ,
                                <span data-bb-toggle="filter-children-count" class="ms-1 me-1">0</span> {{ __('Child(ren)') }},
                                <span data-bb-toggle="filter-rooms-count" class="me-1 ms-1">1</span> {{ __('Room(s)') }}
                            </button>

                            <div class="custom-dropdown dropdown-menu p-3" id="toggle-guests-and-rooms">
                                <div class="inputs-filed">
                                    <label for="adults">{{ __('Adults') }}</label>
                                    <div class="input-quantity">
                                        <button type="button" class="main-btn btn" data-bb-toggle="decrement-room">-</button>
                                        <input type="number" id="adults" name="adults" readonly value="1" min="{{ HotelHelper::getMinimumNumberOfGuests() }}" max="{{ HotelHelper::getMaximumNumberOfGuests() }}">
                                        <button type="button" class="main-btn btn" data-bb-toggle="increment-room">+</button>
                                    </div>
                                </div>
                                <div class="inputs-filed mt-30">
                                    <label for="children">{{ __('Children') }}</label>
                                    <div class="input-quantity">
                                        <button type="button" class="main-btn btn" data-bb-toggle="decrement-room">-</button>
                                        <input type="number" id="children" name="children" readonly value="0" min="0" max="{{ HotelHelper::getMaximumNumberOfGuests() }}">
                                        <button type="button" class="main-btn btn" data-bb-toggle="increment-room">+</button>
                                    </div>
                                </div>
                                <div class="inputs-filed mt-30">
                                    <label for="rooms">{{ __('Rooms') }}</label>
                                    <div class="input-quantity">
                                        <button type="button" class="main-btn btn" data-bb-toggle="decrement-room">-</button>
                                        <input type="number" id="rooms" name="rooms" readonly value="1" min="1" max="10">
                                        <button type="button" class="main-btn btn" data-bb-toggle="increment-room">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CTA --}}
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
                <div class="row booking-area">
                    @if (! empty($title))
                        <div class="col-lg-12">
                            <div class="section-title center-align mb-30">
                                <h2>{!! BaseHelper::clean($title) !!}</h2>
                            </div>
                        </div>
                    @endif

                        <div id="booking-slots" class="booking-slots-wrapper">
                            <div class="slot-item0 slot-item">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="contact-field mb-15">
                                            <label><i class="fal fa-badge-check"></i> {{ __('Check In Time') }}</label>
                                            <div class="input-group date" data-target-input="nearest">
                                                <input
                                                        type="text"
                                                        name="slots[0][start_date]"
                                                        class="theme-date-input-start check-in"
                                                        id="checkin-0"
                                                        autocomplete="off"
                                                        placeholder="DD / MM / YYYY  HH : MM"
                                                        value="{{ BaseHelper::stringify($availableForBooking ? old('start_date', $startDate) : $startDate) }}"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="contact-field mb-15">
                                            <label><i class="fal fa-times-octagon"></i> {{ __('Check Out Time') }}</label>
                                            <div class="input-group date" data-target-input="nearest">
                                                <input
                                                        type="text"
                                                        name="slots[0][end_date]"
                                                        class="theme-date-input-end check-out"
                                                        id="checkout-0"
                                                        autocomplete="off"
                                                        placeholder="DD / MM / YYYY  HH : MM"
                                                        value="{{ BaseHelper::clean($availableForBooking ? old('end_date', $endDate) : $endDate) }}"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                        <div class="mb-3">
                            <button type="button" id="add-slot" class="btn btn-add-slot">
                                + {{ __('Add New') }}
                            </button>
                        </div>

                        {{-- Adults --}}
                    <div class="col-lg-12">
                        <div class="contact-field p-relative c-subject input-group input-group-two left-icon mb-20">
                            <label for="adults"><i class="fal fa-users"></i>{{ __('Adults') }}</label>
                            <div class="input-quantity">
                                <button type="button" class="main-btn btn" data-bb-toggle="decrement-room">-</button>
                                <input type="number" id="adults" name="adults" readonly value="{{ BaseHelper::stringify(request()->integer('adults', 1)) }}" min="{{ HotelHelper::getMinimumNumberOfGuests() }}" max="{{ HotelHelper::getMaximumNumberOfGuests() }}">
                                <button type="button" class="main-btn btn" data-bb-toggle="increment-room">+</button>
                            </div>
                        </div>
                    </div>

                    {{-- CTA --}}
                    <div class="col-lg-12">
                        <div class="slider-btn mt-15">
                            <button type="submit" class="btn ss-btn" data-animation="fadeInRight" data-delay=".8s">
                                <span>{{ $availableForBooking ? __('Book Now') : __('Check Availability') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
        @endswitch
    </form>
@endif
