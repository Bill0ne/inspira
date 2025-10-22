@php
    // ===== Farben =====
    $primary   = '#578E88';
    $secondary = '#000000';
    $accent    = '#C49E6D';

    // ===== Start/Ende auf nächstes 15-Minuten-Intervall runden =====
    $now = \Carbon\Carbon::now()->seconds(0);
    $rounded = (int) ceil($now->minute / 15) * 15;
    $now = $rounded >= 60 ? $now->addHour()->minute(0) : $now->minute($rounded);

    $startVal = $now->format('Y-m-d H:i');              // Wert fürs Backend
    $endVal   = $now->copy()->addHour()->format('Y-m-d H:i');

    // ===== Eindeutige IDs (nur falls du welche brauchst) =====
    $formUid = uniqid('inspira_');
@endphp

{{-- ===== Flatpickr-Assets (LOKAL) ===== --}}
@php
    Theme::asset()->container('footer')->add('fp-css', '/themes/riorelax/flatpickr/flatpickr.min.css');
    Theme::asset()->container('footer')->add('fp-js',  '/themes/riorelax/flatpickr/flatpickr.min.js');
    // Lokalisierung DE (falls vorhanden unter /public/themes/riorelax/flatpickr/l10n/de.js)
    Theme::asset()->container('footer')->add('fp-de',  '/themes/riorelax/flatpickr/l10n/de.js', ['fp-js']);
    // Initialisierung
    Theme::asset()->container('footer')->add('fp-init','/themes/riorelax/flatpickr/init-flatpickr.js', ['fp-js']);
@endphp

<style>
/* ====== Inspira – kompaktes UI ====== */
.inspira-form { --pri: {{ $primary }}; --sec: {{ $secondary }}; --acc: {{ $accent }}; }
.inspira-form .section-title h2 { font-size: 22px; line-height: 26px; margin: 0 0 10px; color: var(--sec); }

/* Feld-Layout */
.inspira-field { margin-bottom: 12px; }
.inspira-label { display:flex; align-items:center; gap:8px; font-weight:600; font-size: 12px; color:#475569; margin-bottom:6px; }
.inspira-input { height: 40px; border-radius: 8px; border:1px solid #d1d5db; padding: 0 12px; width: 100%; font-size: 14px; }
.inspira-input:focus { border-color: var(--pri); box-shadow: 0 0 0 3px color-mix(in srgb, var(--pri) 20%, transparent); outline: none; }

/* Plus/Minus */
.qty { display:flex; align-items:center; gap:6px; }
.qty .btn-qty {
  width:34px; height:34px; border-radius:8px; border:1px solid #d1d5db; background:#fff; color:var(--sec);
  line-height:32px; text-align:center; font-weight:700; cursor:pointer;
}
.qty .btn-qty:hover { border-color: var(--pri); color: var(--pri); }
.qty input[type="number"]{
  width:64px; text-align:center; height:34px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; background:#fff;
}

/* CTA */
.inspira-cta { display:block; width:100%; height:42px; border:none; border-radius:10px; background:var(--pri); color:#fff; font-weight:600; }
.inspira-cta:hover { filter: brightness(0.95); }
.inspira-cta:disabled { background:#cbd5e1; color:#475569; cursor:not-allowed; }

/* Flatpickr – Farbanpassung + Kompakt */
.flatpickr-calendar { z-index: 10550 !important; font-size: 10px; }
.flatpickr-day.selected,
.flatpickr-day.startRange,
.flatpickr-day.endRange { background: var(--pri) !important; border-color: var(--pri) !important; }
.flatpickr-day.today { border-color: var(--acc) !important; }
.flatpickr-months .flatpickr-month { color: var(--sec); }
.flatpickr-weekday { color: #64748b; }
.flatpickr-time .flatpickr-time-separator,
.flatpickr-time input { color: var(--sec); }
.flatpickr-minute:hover, .flatpickr-hour:hover { background: color-mix(in srgb, var(--pri) 12%, white); }

/* Kompaktere Abstände */
.flatpickr-calendar.arrowTop:before, .flatpickr-calendar.arrowTop:after { display:none; }
</style>

{{-- ===== Formular ===== --}}
<div class="inspira-form">
  <form action="{{ $availableForBooking ? route('public.booking') : route('public.rooms') }}"
        method="{{ $availableForBooking ? 'POST' : 'GET' }}"
        class="form-booking" id="{{ $formUid }}">

    @if ($availableForBooking)
      @csrf
      <input type="hidden" name="room_id" value="{{ $room->id }}">
    @endif

    {{-- Titel optional --}}
    @if (! empty($title))
      <div class="section-title center-align">
        <h2>{!! BaseHelper::clean($title) !!}</h2>
      </div>
    @endif

    {{-- Datum/Zeit – ein Set mit Start & Ende --}}
    <div data-fp-set>
      <div class="inspira-field">
        <label class="inspira-label"><i class="fal fa-badge-check"></i> {{ __('Von') }}</label>
        <input data-fp="start"
               name="start_date"
               type="text"
               class="inspira-input"
               value="{{ $startVal }}"
               autocomplete="off">
      </div>

      <div class="inspira-field">
        <label class="inspira-label"><i class="fal fa-times-octagon"></i> {{ __('Bis') }}</label>
        <input data-fp="end"
               name="end_date"
               type="text"
               class="inspira-input"
               value="{{ $endVal }}"
               autocomplete="off">
      </div>
    </div>

    {{-- Adults / Children / Rooms – kompakt --}}
    <div class="inspira-field">
      <label class="inspira-label"><i class="fal fa-users"></i> {{ __('Erwachsene') }}</label>
      <div class="qty">
        <button type="button" class="btn-qty" data-step="-1" data-target="#{{ $formUid }}-adults">−</button>
        <input id="{{ $formUid }}-adults" type="number" name="adults" min="{{ HotelHelper::getMinimumNumberOfGuests() }}" max="{{ HotelHelper::getMaximumNumberOfGuests() }}" value="{{ request()->integer('adults', 1) }}">
        <button type="button" class="btn-qty" data-step="1" data-target="#{{ $formUid }}-adults">+</button>
      </div>
    </div>


    {{-- CTA --}}
    <div class="inspira-field" style="margin-top: 8px;">
      <button type="submit" class="inspira-cta">
        {{ $availableForBooking ? __('Jetzt Buchen') : __('Verfügbarkeit prüfen') }}
      </button>
    </div>
  </form>
</div>

{{-- Kleine, konfliktfreie Logik für +/− --}}
<script>
(function(){
  document.addEventListener('click', function(e){
    const b = e.target.closest('.btn-qty'); if(!b) return;
    const target = document.querySelector(b.getAttribute('data-target')); if(!target) return;
    const step = parseInt(b.getAttribute('data-step'), 10) || 1;
    const min = parseInt(target.min || '0', 10);
    const max = parseInt(target.max || '9999', 10);
    const val = Math.min(max, Math.max(min, (parseInt(target.value || '0', 10) + step)));
    target.value = val;
  }, false);
})();
</script>
