@php
    $margin = $margin ?? false;
    $canShowRoomPrices = HotelHelper::canShowRoomPrices();
    $configuredPrice = $canShowRoomPrices ? HotelHelper::getRoomConfiguredPrice($room) : null;
    $displayPriceDiffers = false;
    $priceUnitLabel = __('hour_lowercase');
    $priceInquiryText = HotelHelper::getRoomPriceInquiryText();
    $priceInquiryUrl = 'https://inspira-zentrum.net/de/nimm-kontakt-mit-uns-auf';

    /* === Fallback-Bild === */
    $image = $room->images && count($room->images) > 0
        ? RvMedia::getImageUrl(Arr::first($room->images), 'medium')
        : RvMedia::getImageUrl('default-room.jpg', 'medium', false, RvMedia::getDefaultImage());
@endphp

<style>
/* === Inspira – Room Card (gleiches Design wie Course) === */
.room-card {
  background: #fff !important;
  border-radius: 10px !important;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06) !important;
  overflow: hidden !important;
  transition: transform 0.25s ease, box-shadow 0.25s ease !important;
  cursor: pointer !important;
  display: flex;
  flex-direction: column;
}
.room-card:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08) !important;
}

/* === Bild === */
.room-thumb {
  padding: 12px !important;
}
.room-thumb img {
  width: 100% !important;
  border-radius: 14px !important;
  display: block !important;
  object-fit: cover !important;
  aspect-ratio: 16 / 9 !important;
  background-color: #f2f2f2 !important;
}

/* === Body === */
.room-body {
  padding: 10px 16px 12px 16px !important;
  display: flex;
  flex-direction: column;
  gap: 6px !important;
}
.room-title {
  font-size: 16px !important;
  line-height: 20px !important;
  font-weight: 600 !important;
  color: #414141 !important;
  margin: 0 0 2px 0 !important;
}
.room-desc {
  font-size: 12px !important;
  line-height: 18px !important;
  color: #6C6C6C !important;
  font-weight: 400 !important;
  margin: 0 0 8px 0 !important;
  display: -webkit-box !important;
  -webkit-line-clamp: 2 !important;
  -webkit-box-orient: vertical !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}

/* === Amenities (Icons) === */
.room-icons {
  display: flex !important;
  gap: 8px !important;
  flex-wrap: wrap !important;
  margin-bottom: 6px !important;
}
.room-icons img {
  width: 20px !important;
  height: 20px !important;
  object-fit: contain !important;
  opacity: 0.9 !important;
}

/* === Footer === */
.room-footer {
  display: flex;
  flex-direction: column !important;
  gap: 6px !important;
  margin-top: auto !important;
  padding: 0 16px 12px 16px !important;
}

.room-price {
  display: flex !important;
  align-items: baseline !important;
  gap: 6px !important;
  font-weight: 600 !important;
  color: #578E88 !important;
  font-size: 16px !important;
}

.room-price__value {
  font-size: 16px !important;
}

.room-price--placeholder {
  font-size: 13px !important;
  font-weight: 500 !important;
  color: #6C6C6C !important;
}

/* CTA */
.btn-room-cart {
  background: #578E88 !important;
  color: #fff !important;
  padding: 10px 0 !important;
  border-radius: 6px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  width: 100% !important;
  font-size: 14px !important;
  font-weight: 500 !important;
  transition: background 0.2s ease !important;
}
.btn-room-cart:hover {
  background: #4B7C75 !important;
}

/* Mehr Infos */
.more-link {
  font-size: 12px !important;
  font-weight: 500 !important;
  color: #578E88 !important;
  text-decoration: underline !important;
  text-underline-offset: 2px !important;
  text-align: center !important;
  display: block !important;
  margin-top: 4px !important;
}
.more-link:hover {
  text-underline-offset: 3px !important;
}
</style>

<div class="room-card" onclick="window.location='{{ $room->url }}'">
  {{-- === Bild === --}}
  <div class="room-thumb">
    <img src="{{ $image }}" alt="{{ $room->name }}">
  </div>

  {{-- === Inhalt === --}}
  <div class="room-body">
    <h4 class="room-title">{{ $room->name }}</h4>

    @if ($room->description)
      <p class="room-desc">{!! BaseHelper::clean(strip_tags(Str::limit($room->description, 120))) !!}</p>
    @endif

    {{-- === Amenities === --}}
    @if ($room->amenities->isNotEmpty())
      <div class="room-icons">
        @foreach ($room->amenities->take(5) as $amenity)
          @if ($icon = $amenity->getMetaData('icon_image', true))
            <img src="{{ RvMedia::getImageUrl($icon) }}" alt="{{ $amenity->name }}">
          @endif
        @endforeach
      </div>
    @endif
  </div>

  {{-- === Footer === --}}
  <div class="room-footer">
    @if ($canShowRoomPrices)
      <div class="room-price">
        <span class="room-price__value">{{ __(':price / :unit', ['price' => format_price($configuredPrice), 'unit' => $priceUnitLabel]) }}</span>
      </div>
    @else
      <div class="room-price room-price--placeholder">{!! $priceInquiryText !!}</div>
    @endif

    @if (HotelHelper::isBookingEnabled())
      <a
        href="{{ $canShowRoomPrices
            ? $room->url . '?start_date=' . BaseHelper::stringify(request()->query('start_date', $startDate)) . '&end_date=' . BaseHelper::stringify(request()->query('end_date', $endDate))
            : $priceInquiryUrl }}"
        class="btn-room-cart"
        onclick="event.stopPropagation()"
      >
        <i class="fal fa-calendar-check me-2"></i>
        {{ $canShowRoomPrices ? __('Jetzt buchen') : __('Anfragen') }}
      </a>
    @endif

    <a class="more-link" href="{{ $room->url }}" onclick="event.stopPropagation()">
      {{ __('Mehr Infos') }}
    </a>
  </div>
</div>
