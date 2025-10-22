@php
    $margin = $margin ?? false;
    $isLoggedIn = auth('customer')->check() || auth()->check();
@endphp

<style>
/* ===== Room-Karte (gleiches UI wie Course, ohne Chips) ===== */
.single-services.room-card .services-thumb img{
  width:100%;
  height:auto;
  display:block;
}

/* Inhalt als Spalte; kompakte Innenabstände */
.single-services.room-card .services-content{
  display:flex;
  flex-direction:column;
  min-height:100%;
  padding-top:5px;
  padding-bottom:5px;
  gap:6px;
}

/* Titel & Beschreibung (letzte Version: +2px) */
.single-services.room-card h4{
  margin:2px 0;
  font-size:17px;
  line-height:22px;
  font-weight:600;
}
.single-services.room-card .room-item-custom-truncate{
  margin:0 0 4px 0;
  font-size:13px;
  line-height:18px;
  color:#6B7280;
  overflow:hidden;
}

/* Amenities / Icons – **2px** Abstand zum CTA */
.single-services.room-card .icon{
  margin:2px 0 2px 0;   /* 2px unten -> direkt vor CTA */
}
.single-services.room-card .icon ul{
  display:flex;
  flex-wrap:wrap;
  gap:8px 10px;
  justify-content:flex-start;
  padding:0;
  margin:0;
  list-style:none;
}
.single-services.room-card .icon li{
  display:flex; align-items:center; justify-content:center;
}
.single-services.room-card .icon img{
  display:block; width:20px; height:20px; object-fit:contain;
}

/* ===== Footer-Aktionen: CTA + Mehr Infos (Mehr Infos unter CTA) ===== */
.single-services.room-card .services-content .footer-actions{
  margin-top:0;               /* keine Extra-Lücke oben */
  padding-top:0;
  display:flex;
  flex-direction:column;
  align-items:center;
  gap:6px;                    /* Abstand zwischen CTA und Link */
}

/* CTA-Block – Button nutzt ORIGINAL-Padding des Themes */
.single-services.room-card .services-content .day-book{ width:100%; margin:0; padding:0; }
.single-services.room-card .services-content .day-book ul{ margin:0; padding:0; list-style:none; }
.single-services.room-card .services-content .day-book li{ margin:0; padding:0; }
.single-services.room-card .services-content .day-book .book-button-custom{
  display:block;
  width:100%;
  margin:0;                   /* keine Außenabstände */
  /* kein padding-Override -> Original bleibt */
}

/* Mehr Infos – unter CTA, zentriert, dezentes Padding */
.single-services.room-card .services-content .more-link{
  font-size:12px;
  font-weight:500;
  color:#578E88;
  text-decoration:underline;
  text-underline-offset:2px;
  text-decoration-thickness:1px;

  display:inline-block;
  padding:3px 6px;           /* kompakte Klickfläche */
  margin:0;
  line-height:16px;
  text-align:center;

  background:none; border:0; box-shadow:none;
}
.single-services.room-card .services-content .more-link:hover{
  text-underline-offset:3px;
}
</style>

<div @class(['single-services shadow-block mb-30 room-card', 'ser-m' => !$margin])>
  <div class="services-thumb hover-zoomin wow fadeInUp animated">
    @if ($images = $room->images)
      <a href="{{ $room->url }}?start_date={{ BaseHelper::stringify(request()->query('start_date', $startDate)) }}&end_date={{ BaseHelper::stringify(request()->query('end_date', $endDate)) }}&adults={{ BaseHelper::stringify(request()->query('adults', HotelHelper::getMinimumNumberOfGuests())) }}&children={{ BaseHelper::stringify(request()->query('children', 0)) }}">
        <img src="{{ RvMedia::getImageUrl(Arr::first($images), 'medium') }}" alt="{{ $room->name }}">
      </a>
    @endif
  </div>

  <div class="services-content">
    {{-- Titel & Beschreibung --}}
    <h4><a href="{{ $room->url }}">{{ $room->name }}</a></h4>

    @if ($description = $room->description)
      <p class="room-item-custom-truncate" title="{{ $description }}">
        {!! BaseHelper::clean($description) !!}
      </p>
    @endif

    {{-- Amenities / Icons --}}
    @if ($room->amenities->isNotEmpty())
      <div class="icon">
        <ul>
          @foreach ($room->amenities->take(6) as $amenity)
            @if ($image = $amenity->getMetaData('icon_image', true))
              <li><img src="{{ RvMedia::getImageUrl($image) }}" alt="{{ $amenity->name }}"></li>
            @endif
          @endforeach
        </ul>
      </div>
    @endif

    {{-- Footer: CTA direkt nach Icons (2px), Mehr Infos darunter --}}
    <div class="footer-actions">
      @if (HotelHelper::isBookingEnabled())
        <div class="day-book">
          <ul>
            <li>
              @if ($isLoggedIn)
                <a
                  href="{{ $room->url }}?start_date={{ BaseHelper::stringify(request()->query('start_date', $startDate)) }}&end_date={{ BaseHelper::stringify(request()->query('end_date', $endDate)) }}&adults={{ BaseHelper::stringify(request()->query('adults', HotelHelper::getMinimumNumberOfGuests())) }}&children={{ BaseHelper::stringify(request()->query('children', 0)) }}"
                  class="book-button-custom d-inline-block text-center"
                  style="width:100%;"
                  data-animation="fadeInRight"
                  data-delay=".8s"
                >{{ __('Jetzt Buchen') }}</a>
              @else
                <a
                  href="https://inspira-zentrum.net/de/nimm-kontakt-mit-uns-auf"
                  class="book-button-custom d-inline-block text-center"
                  style="width:100%;"
                  data-animation="fadeInRight"
                  data-delay=".8s"
                >{{ __('Anfragen') }}</a>
              @endif
            </li>
          </ul>
        </div>
      @endif

      <a class="more-link" href="{{ $room->url }}" aria-label="Mehr Infos zu {{ $room->name }}">
        {{ __('Mehr Infos') }}
      </a>
    </div>

  </div>
</div>
