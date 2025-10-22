@php
    $primary   = '#578E88';
    $formUid   = uniqid('inspira_');

    // Now rounded to next 15 mins
    $now = \Carbon\Carbon::now()->seconds(0);
    $rounded = (int) ceil($now->minute / 15) * 15;
    $now = $rounded >= 60 ? $now->addHour()->minute(0) : $now->minute($rounded);

    $prefillDate  = $now->format('d.m.Y');
    $prefillStart = $now->format('H:i');
    $prefillEnd   = $now->copy()->addHour()->format('H:i');
@endphp

{{-- Flatpickr assets (LOCAL). CSS in HEADER to avoid FOUC, JS in FOOTER. --}}
@php
    Theme::asset()->container('header')->add('fp-css', '/themes/riorelax/flatpickr/flatpickr.min.css');
    Theme::asset()->container('footer')->add('fp-js',  '/themes/riorelax/flatpickr/flatpickr.min.js');
    Theme::asset()->container('footer')->add('fp-de',  '/themes/riorelax/flatpickr/l10n/de.js', ['fp-js']);
@endphp

<style>
/* --- Compact form --- */
.inspira-form{--pri:{{ $primary }}}
.inspira-form .section-title h2{margin:0 0 8px;font-size:18px;line-height:22px}
.slot-row{display:flex;gap:8px;flex-wrap:wrap;border:1px dashed #d1d5db;border-radius:10px;background:#f8fafc;padding:10px}
.slot-pill{display:flex;align-items:center;gap:8px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;height:40px;padding:0 12px}
.slot-pill input{border:0;outline:0;height:100%;font-size:14px;width:110px}
.slot-pill.time input{width:64px;text-align:center}
.inspira-cta{display:block;width:100%;height:44px;border:0;border-radius:10px;background:var(--pri);color:#fff;font-weight:600}
.inspira-cta:hover{filter:brightness(.95)}
/* Flatpickr tweak */
.flatpickr-calendar{z-index:10550!important;font-size:12px}
.flatpickr-day.selected,.flatpickr-day.startRange,.flatpickr-day.endRange{background:var(--pri)!important;border-color:var(--pri)!important;color:#fff!important}
.flatpickr-day.today{border-color:var(--pri)!important}
</style>

<div class="inspira-form">
  <div class="section-title"><h2>{{ __('Buchungsformular') }}</h2></div>
  <p style="margin:0 0 6px;color:#334155;font-size:14px">{{ __('Bitte einen Slot buchen:') }}</p>

  <form id="{{ $formUid }}"
        class="form-booking"
        action="{{ $availableForBooking ? route('public.booking') : route('public.rooms') }}"
        method="{{ $availableForBooking ? 'POST' : 'GET' }}">
    @if ($availableForBooking)
      @csrf
      <input type="hidden" name="room_id" value="{{ $room->id }}">
    @endif

    <div class="slot-row">
      <label class="slot-pill">
        <span class="ti ti-calendar" aria-hidden="true"></span>
        <input type="text" class="js-date" placeholder="TT.MM.JJJJ" value="{{ $prefillDate }}" autocomplete="off" readonly>
      </label>
      <label class="slot-pill time">
        <span class="ti ti-clock" aria-hidden="true"></span>
        <input type="text" class="js-start" placeholder="HH:MM" value="{{ $prefillStart }}" autocomplete="off" readonly>
      </label>
      <label class="slot-pill time">
        <span class="ti ti-clock" aria-hidden="true"></span>
        <input type="text" class="js-end" placeholder="HH:MM" value="{{ $prefillEnd }}" autocomplete="off" readonly>
      </label>
    </div>

    {{-- Hidden fields that the backend expects --}}
    <input type="hidden" name="start_date">
    <input type="hidden" name="end_date">

    <div style="margin-top:10px">
      <button class="inspira-cta" type="submit">
        {{ $availableForBooking ? __('Jetzt Buchen') : __('Verfügbarkeit prüfen') }}
      </button>
    </div>
  </form>
</div>

<script>
(function(){
  function onReady(fn){document.readyState!=='loading'?fn():document.addEventListener('DOMContentLoaded',fn);}

  onReady(function(){
    if(!window.flatpickr){ console.error('Flatpickr fehlt: Prüfe /themes/riorelax/flatpickr/* Pfade.'); return; }
    flatpickr.localize(flatpickr.l10ns.de || {});

    const form   = document.getElementById('{{ $formUid }}');
    const $date  = form.querySelector('.js-date');
    const $start = form.querySelector('.js-start');
    const $end   = form.querySelector('.js-end');

    // Date picker (calendar)
    flatpickr($date, {
      dateFormat: 'd.m.Y',
      allowInput: false,
      clickOpens: true,
      defaultDate: $date.value || new Date(),
    });

    // Time pickers (24h)
    const timeCfg = {
      enableTime: true,
      noCalendar: true,
      dateFormat: 'H:i',
      time_24hr: true,
      minuteIncrement: 15,
      allowInput: false,
      clickOpens: true,
    };
    flatpickr($start, Object.assign({}, timeCfg, { defaultDate: $start.value || '10:00' }));
    flatpickr($end,   Object.assign({}, timeCfg, { defaultDate: $end.value   || '12:00' }));

    // Map to backend format d-m-Y h:i A
    function parseDMY(str){
      const [dd,mm,yy] = str.split('.');
      return new Date(+yy, (mm|0)-1, dd|0);
    }
    function to12(hhmm){
      const [H,M] = hhmm.split(':').map(n=>parseInt(n,10));
      const suf = H >= 12 ? 'PM' : 'AM';
      const h12 = ((H + 11) % 12 + 1);
      return `${String(h12).padStart(2,'0')}:${String(M).padStart(2,'0')} ${suf}`;
    }
    function fmtFinal(d, hm){ // d-m-Y h:i A
      const dd = String(d.getDate()).padStart(2,'0');
      const mm = String(d.getMonth()+1).padStart(2,'0');
      const yy = d.getFullYear();
      return `${dd}-${mm}-${yy} ${to12(hm)}`;
    }

    form.addEventListener('submit', function(e){
      const d = $date.value.trim();
      const s = $start.value.trim();
      const t = $end.value.trim();
      if(!d || !s || !t){
        e.preventDefault();
        alert('Bitte Datum, Start- und Endzeit wählen.');
        return;
      }
      const dObj = parseDMY(d);
      const startVal = fmtFinal(dObj, s);
      const endVal   = fmtFinal(dObj, t);

      form.querySelector('[name="start_date"]').value = startVal; // d-m-Y h:i A
      form.querySelector('[name="end_date"]').value   = endVal;

      const re = /^\d{2}-\d{2}-\d{4} \d{2}:\d{2} (AM|PM)$/;
      if(!re.test(startVal) || !re.test(endVal)){
        e.preventDefault();
        console.warn('Formatfehler', {startVal, endVal});
        alert('Zeitformat ungültig. Bitte erneut wählen.');
      }
    });
  });
})();
</script>
