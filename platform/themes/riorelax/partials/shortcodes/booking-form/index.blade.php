@php
    Theme::asset()->container('header')->usePath()->add('date-css', 'css/date.css');
        Theme::asset()->container('footer')->add('popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js', ['jquery']);
        Theme::asset()->container('footer')->add('bootstrap-js', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.bundle.min.js', ['jquery', 'popper']);
      Theme::asset()->container('footer')->usePath()->add('moment-js', 'vendors/moment.min.js');
      Theme::asset()->container('footer')->usePath()->add('date-picker', 'vendors/date-picker.min.js');
        Theme::asset()->container('footer')->usePath()->add('datetime-js', 'js/datetime.js');
@endphp
<section class="booking pt-90 pb-90 p-relative fix">
    @if ($shapeImage = $shortcode->shape_image)
        <div class="animations-01">
            <img src="{{ RvMedia::getImageUrl($shapeImage) }}" alt="{{ __('Shape image') }}">
        </div>
    @endif
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-6">
                <div class="contact-bg02">
                    <div class="section-title center-align">
                        @if ($subtitle = $shortcode->subtitle)
                            <h5>{!! BaseHelper::clean($subtitle) !!}</h5>
                        @endif
                        @if ($title = $shortcode->title)
                            <h2>{!! BaseHelper::clean($title) !!}</h2>
                        @endif
                    </div>
                    <form action="{{ route('public.booking') }}" method="post" class="contact-form mt-30 form-booking">
                        @csrf
                        <div class="row">
                            <div id="booking-slots" class="booking-slots-wrapper">
                                <div class="slot-item0 slot-item">
                                    <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <div class="contact-field p-relative c-name mb-20">
                                    <label for="booking-form-start-date"><i
                                            class="fal fa-badge-check"></i>{{ __('Check In Date') }}</label>
                                    <input
                                            type="text"
                                            name="slots[0][start_date]"
                                            class="theme-date-input-start check-in"
                                            id="checkin-0"
                                            autocomplete="off"
                                            placeholder="DD / MM / YYYY  HH : MM"
                                           value="{{ old('start_date', Carbon\Carbon::now()->format(HotelHelper::getDateFormat())) }}"
                                    >
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6">
                                <div class="contact-field p-relative c-subject mb-20">
                                    <label for="booking-form-end-date"><i
                                            class="fal fa-times-octagon"></i>{{ __('Check Out Date') }}</label>
                                    <input
                                            type="text"
                                            name="slots[0][end_date]"
                                            class="theme-date-input-end check-out"
                                            id="checkout-0"
                                            autocomplete="off"
                                            placeholder="DD / MM / YYYY  HH : MM"
                                           value="{{ BaseHelper::stringify(old('end_date', Carbon\Carbon::now()->addDay()->format(HotelHelper::getDateFormat()))) }}">
                                </div>
                            </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="contact-field p-relative c-subject mb-20">
                                    <label for="adults"><i class="fal fa-users"></i>{{ __('Guests') }}</label>
                                    <select name="adults" id="adults">
                                        @for($i = 1; $i <= 14; $i++)
                                            <option value="{{ $i }}" @selected(old('adults', HotelHelper::getMinimumNumberOfGuests()) === 1)>{{ $i }} {{ $i == 1 ? __('Guest') : __('Guests') }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="contact-field p-relative c-option mb-20">
                                    <label for="room"><i class="fal fa-concierge-bell"></i>{{ __('Room') }}</label>
                                    <select name="room_id" id="room">
                                        @foreach($rooms as $key => $value)
                                            <option @selected(old('room_id') === $key) value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="slider-btn mt-15">
                                    <button type="submit" class="btn ss-btn" data-animation="fadeInRight"
                                            data-delay=".8s">
                                        <span>{{ __('Book now') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                @if ($image = $shortcode->image)
                    <div class="booking-img">
                        <img src="{{ RvMedia::getImageUrl($image) }}" alt="{{ __('Image') }}">
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

