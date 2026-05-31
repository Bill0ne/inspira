@php
    Theme::asset()->container('footer')->usePath()->add('lightgallery-css', 'plugins/lightgallery/css/lightgallery.min.css');
    Theme::asset()->container('footer')->usePath()->add('lightgallery-js', 'plugins/lightgallery/js/lightgallery.min.js');
        Theme::asset()->container('footer')->usePath()->add('checkout-js', 'js/course-checkout.js');


    Theme::set('pageTitle', $room->name);
    $nights = max(1, $startDate->diffInHours($endDate));
    $canShowRoomPrices = HotelHelper::canShowRoomPrices();
    $configuredPrice = $canShowRoomPrices ? HotelHelper::getRoomConfiguredPrice($room) : null;
    $displayPriceDiffers = false;
    $priceUnitLabel = __('hour_lowercase');
    $priceInquiryText = HotelHelper::getRoomPriceInquiryText();
@endphp
<style>
    /*
     * Galerie-Thumbnails: feste Höhe + object-fit, damit die (vertikale) Slick-
     * Thumbnail-Leiste nicht auf Höhe 0 kollabiert. Slick berechnet die Höhe der
     * vertikalen Leiste beim Init aus den Slide-Höhen; ohne feste Höhe sind die
     * Bilder beim Init noch nicht geladen → Höhe 0 → Thumbnails unsichtbar.
     * Spiegelt die Regel in assets/sass/_custom.scss (.room-details--rooms ...),
     * hier inline, damit der Fix auch ohne Asset-Neukompilierung sofort greift.
     */
    .room-details--rooms .room-details-slider-nav img {
        height: 96px;
        object-fit: cover;
        border-radius: 2px;
    }

    @media (max-width: 991.98px) {
        .room-details--rooms .room-details-slider-nav img {
            height: 80px;
        }
    }
</style>
<div class="about-area5 about-p p-relative room-details room-details--rooms">
    <div class="container pt-60 pb-40">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="service-detail">
                    <div class="thumb">
                        <div class="room-details-slider">
                            @foreach ($room->images as $img)
                                <a href="{{ RvMedia::getImageUrl($img) }}">
                                    <img src="{{ RvMedia::getImageUrl($img, 'room-image') }}" alt="{{ $room->name }}">
                                </a>
                            @endforeach
                        </div>
                        <div class="room-details-slider-nav">
                            @foreach ($room->images as $img)
                                <img src="{{ RvMedia::getImageUrl($img, 'thumb') }}" alt="{{ $room->name }}">
                            @endforeach
                        </div>
                    </div>
                    <div class="content-box">
                        <div class="room-header">
                            <h2 class="room-header__title">{{ $room->name }}</h2>
                        </div>

                        <div class="room-booking-card shadow-block">
                            <div class="room-booking-card__pricing">
                            {{-- Preis nur anzeigen, wenn dem Kunden eine Kategorie zugeordnet ist --}}
                            @if ($canShowRoomPrices)
                                <div class="room-booking-card__price-chip">
                                    {{ __(':price / :unit', ['price' => format_price($configuredPrice), 'unit' => $priceUnitLabel]) }}
                                </div>
                                @if ($displayPriceDiffers)
                                    <div class="room-booking-card__price-original text-muted text-decoration-line-through small">
                                        {{ format_price($room->price) }}
                                    </div>
                                @endif
                            @else
                                <p class="room-booking-card__notice text-muted">{!! $priceInquiryText !!}</p>
                            @endif
                        </div>

                        @if (HotelHelper::isBookingEnabled())
                            <div class="room-booking-card__form">
                                @if ($canShowRoomPrices)
                                    {!! Theme::partial('hotel.forms.form', ['availableForBooking' => true, 'style' => 1, 'room' => $room]) !!}
                                @else
                                    <div class="room-booking-card__cta">
                                        <button
                                            type="button"
                                            class="room-booking-card__cta-btn"
                                            data-room-request-trigger
                                            data-room-id="{{ $room->id }}"
                                        >
                                            {{ __('Anfragen') }}
                                        </button>
                                    </div>
                                @endif
                                </div>
                            @else
                                <p class="room-booking-card__notice text-muted mb-0">
                                    {{ __('Booking is currently unavailable.') }}
                                </p>
                            @endif
                        </div>

                        {!! BaseHelper::clean($room->content) !!}

                        @if ($room->amenities->isNotEmpty())
                            <div class="room-block-content shadow-block mt-50 amenities-list">
                                <h3>{{ __('Amenities') }}</h3>
                                <div class="row">
                                    @foreach ($room->amenities as $amenity)
                                        @php
                                            $image = $amenity->getMetaData('icon_image', true)
                                        @endphp

                                        <div class="col-xl-4 col-lg-6 col-12 d-flex align-items-center mb-3">
                                            @if ($image)
                                                <img width="20px" class="d-block" src="{{ RvMedia::getImageUrl($image) }}" alt="{{ $amenity->name }}">
                                            @elseif($amenity->icon)
                                                <x-core::icon :name="$amenity->icon"/>
                                            @endif
                                            <span class="ms-2">{{ $amenity->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($rules = theme_option('hotel_rules'))
                            <div class="room-block-content shadow-block">
                                <div class="hotel-rules-box">
                                    <h3>{{ __('Hotel Rules') }}</h3>
                                    {!! BaseHelper::clean($rules) !!}
                                </div>
                            </div>
                        @endif

                        @if ($cancellation = theme_option('cancellation'))
                            <div class="room-block-content shadow-block">
                                <h3>{{ __('Cancellation') }}</h3>
                                {!! BaseHelper::clean($cancellation) !!}
                            </div>
                        @endif

                        @if(HotelHelper::isReviewEnabled())
                            @include(Theme::getThemeNamespace('views.hotel.partials.reviews'), ['model' => $room])
                        @endif

                        @if($relatedRooms->isNotEmpty())
                            <div class="content-box related-room">
                                <h3>{{ __('Related Rooms') }}</h3>
                                <div class="row g-4">
                                    @foreach($relatedRooms as $relatedRoom)
                                        <div class="col-12 col-sm-6 col-lg-3">
                                            {!! Theme::partial('rooms.item', [
                                                'room' => $relatedRoom,
                                                'startDate' => $startDate,
                                                'endDate' => $endDate,
                                                'nights' => $nights,
                                                'adults' => $adults,
                                            ]) !!}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

