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
    <form action="{{ $availableForBooking ? route('public.booking') : route('public.rooms') }}" method="{{ $availableForBooking ? 'POST' : 'GET' }}" class="contact-form mt-30 form-booking">
        @if ($availableForBooking)
            @csrf
            <input type="hidden" name="room_id" value="{{ $room->id }}">
        @endif


                <div class="row booking-area">

                    <div id="booking-slots" class="booking-slots-wrapper">
                        <div class="slot-item0 slot-item">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="contact-field mb-15">
                                        <label><i class="fal fa-badge-check"></i> {{ __('Check In Time') }}</label>
                                        <div class="input-group date" data-target-input="nearest">
                                            <input
                                                    type="text"
                                                    name="start_date"
                                                    class="theme-date-input-start check-in"
                                                    id="checkin-0"
                                                    autocomplete="off"
                                                    value="{{ request()->query('start_date') }}"
                                                    placeholder="DD / MM / YYYY  HH : MM"
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
                                                    name="end_date"
                                                    class="theme-date-input-end check-out"
                                                    id="checkout-0"
                                                    autocomplete="off"
                                                    value="{{ request()->query('end_date') }}"
                                                    placeholder="DD / MM / YYYY  HH : MM"
                                            />
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

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

                    <div class="col-lg-12">
                        <div class="slider-btn mt-15">
                            <button type="submit" class="btn ss-btn" data-animation="fadeInRight" data-delay=".8s">
                                <span>{{ $availableForBooking ? __('Book Now') : __('Check Availability') }}</span>
                            </button>
                        </div>
                    </div>
                </div>

    </form>
@endif
