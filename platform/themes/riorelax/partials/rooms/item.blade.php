@php
    $margin = $margin ?? false;
    $isLoggedIn = auth('customer')->check() || auth()->check();

    /* === Fallback-Bild === */
    $image = $room->images && count($room->images) > 0
        ? RvMedia::getImageUrl(Arr::first($room->images), 'medium')
        : RvMedia::getImageUrl('default-room.jpg', 'medium', false, RvMedia::getDefaultImage());
@endphp

<div class="room-card" role="link" tabindex="0"
     onclick="window.location='{{ $room->url }}'"
     onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();window.location='{{ $room->url }}';}">
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
    @if (HotelHelper::isBookingEnabled())
      <a 
        href="{{ $isLoggedIn 
            ? $room->url . '?start_date=' . BaseHelper::stringify(request()->query('start_date', $startDate)) . '&end_date=' . BaseHelper::stringify(request()->query('end_date', $endDate)) 
            : 'https://inspira-zentrum.net/de/nimm-kontakt-mit-uns-auf' }}"
        class="btn-room-cart"
        onclick="event.stopPropagation()"
      >
        <i class="fal fa-calendar-check me-2"></i>
        {{ $isLoggedIn ? __('Jetzt buchen') : __('Anfragen') }}
      </a>
    @endif

    <a class="more-link" href="{{ $room->url }}" onclick="event.stopPropagation()">
      {{ __('Mehr Infos') }}
    </a>
  </div>
</div>
