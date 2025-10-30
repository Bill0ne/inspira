@php
    $course   = $booking->course;
    $session  = $booking->session;

    if ($booking->customer && $booking->customer->id) {
        $fullName = trim($booking->customer->first_name.' '.$booking->customer->last_name);
        $email    = $booking->customer->email;
        $phone    = $booking->customer->phone;
    } elseif ($booking->address) {
        $fullName = trim($booking->address->first_name.' '.$booking->address->last_name);
        $email    = $booking->address->email;
        $phone    = $booking->address->phone;
    } else {
        $fullName = __('Guest');
        $email = $phone = null;
    }

    $startLabel24 = $session ? BaseHelper::formatDate($session->start_date, 'd.m.Y H:i') : null;
    $endLabel24   = ($session && $session->end_date) ? BaseHelper::formatDate($session->end_date, 'd.m.Y H:i') : null;

    $instructor = $course?->instructor;
@endphp

<style>
/* ================== Booking Ticket (scoped) ================== */
.booking-ticket-scope{
  --mint:#578E88;
  --chip-bg:#F3F3F3;
  --chip-fg:#578E88;
  --ink:#0F172A;
  --header-h:64px;
  --hole:24px;
}
.booking-ticket{
  display:grid;
  grid-template-columns: 0.95fr 1.15fr 0.68fr; /* left | middle | right */
  grid-template-rows: var(--header-h) auto;    /* header | content */
  grid-template-areas:
    "header header header"
    "left   middle  right";
  background:#fff; border-radius:12px; overflow:hidden;
  box-shadow:0 8px 24px rgba(0,0,0,.06); margin-bottom:24px;
  position:relative;
}

/* Headerband */
.booking-ticket__header{
  grid-area:header;
  background:var(--mint);
  display:flex; align-items:center; gap:14px;
  padding:0 18px; position:relative;
  z-index: 1; /* über dem schwarzen Panel */
}
.booking-ticket__title{
  margin-left:auto;                /* nach rechts schieben */
  color:#fff;                      /* weiß */
  background:none;                 /* kein Label-Background */
  padding:0; border-radius:0;
  font:700 20px/1.2 system-ui;
}

/* Spalten */
.booking-ticket__left{ grid-area:left; padding:0; position:relative; z-index:3; background:#fff; }
.booking-ticket__left .hero{
  width:100%; height:100%; min-height:260px; object-fit:cover; display:block;
}
.booking-ticket__middle{ grid-area:middle; background:#F8FAFB; padding:18px 20px; position:relative; z-index:3; }
.booking-ticket__right{ grid-area:right; background:#000; color:#fff; padding:18px; position:relative; z-index:0; } /* ganz hinten */

/* Mittlere Inhalte – ohne Box-Hintergründe */
.booking-ticket .subcap{ font:700 11px/1 system-ui; color:#9CA3AF; letter-spacing:.02em; text-transform:uppercase; }
.booking-ticket .name-big{ font:700 18px/22px system-ui; color:var(--ink); margin:4px 0 12px; }

.booking-ticket .field{ margin:8px 0 14px; }
.booking-ticket .field .val{ font:700 18px/22px system-ui; color:var(--ink); }
.booking-ticket .field small{ display:block; margin-top:4px; font:400 13px/16px system-ui; color:#94A3B8; }

/* Kundendaten (ohne Box) */
.booking-ticket .customer{ margin:6px 0 10px; }
.booking-ticket .customer .meta{ display:flex; gap:16px; flex-wrap:wrap; }
.booking-ticket .customer .meta a{ color:var(--ink); text-decoration:underline; text-underline-offset:2px; }

/* Instructor-Zeile (ohne Box) */
.booking-ticket .instructor{
  display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin:6px 0 8px;
}
.booking-ticket .instructor .lbl{ font:700 10px/1 system-ui; color:#9CA3AF; text-transform:uppercase; margin-bottom:6px; }
.booking-ticket .instructor .txt{ font:500 13px/16px system-ui; color:var(--ink); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.booking-ticket .instructor a{ color:var(--ink); text-decoration:underline; text-underline-offset:2px; }

/* Chips */
.booking-ticket .chips{ margin-top:10px; display:flex; gap:8px; flex-wrap:wrap; }
.booking-ticket .chip{
  background:var(--chip-bg); color:var(--chip-fg);
  font:500 12px/14px system-ui; padding:7px 12px; border-radius:0;
  display:inline-flex; align-items:center; gap:6px; white-space:nowrap;
}

/* Rechte Spalte (Preis) */
.booking-ticket .right-title{ margin:0 0 10px; font:700 16px/20px system-ui; color:#fff; }
.booking-ticket .kv{ display:flex; justify-content:space-between; gap:10px; margin:6px 0; }
.booking-ticket .kv span{ color:#C7CED6; font:400 12px/14px system-ui; }
.booking-ticket .kv b{ color:#fff; font:600 13px/16px system-ui; }
.booking-ticket .divider{ height:1px; background:rgba(255,255,255,.15); margin:12px 0; }
.booking-ticket .total{ display:flex; justify-content:space-between; font:700 18px/22px system-ui; color:#fff; }
.booking-ticket .status-wrap{ margin-top:18px; display:flex; justify-content:flex-end; }

/* Perforation – durch Header, Loch ganz oben und ganz unten */
.booking-ticket .booking-ticket__right::before{
  content:"";
  position:absolute; left:-1px; top:-100px; bottom:-100px;
  border-left:2px dashed #fff; z-index:4;
}
.booking-ticket .booking-ticket__middle::before,
.booking-ticket .booking-ticket__middle::after{
  content:""; position:absolute; right:-12px; width:var(--hole); height:var(--hole);
  border-radius:50%; background:#fff; box-shadow:0 0 0 1px rgba(0,0,0,.06) inset; z-index:5;
}
.booking-ticket .booking-ticket__middle::before{ top:-12px; }   /* Loch bündig GANZ OBEN */
.booking-ticket .booking-ticket__middle::after { bottom:-12px; } /* und unten */

/* Responsive */
@media (max-width: 900px){
  .booking-ticket{
    grid-template-columns:1fr;
    grid-template-rows: var(--header-h) auto auto auto;
    grid-template-areas:
      "header"
      "left"
      "middle"
      "right";
  }
  .booking-ticket .booking-ticket__right::before,
  .booking-ticket .booking-ticket__middle::before,
  .booking-ticket .booking-ticket__middle::after{ display:none; }
}
</style>

<div class="booking-ticket-scope">
  <div class="booking-ticket">

    {{-- Headerband mit Logo links, Titel rechts (weiß, kein BG) --}}
    <div class="booking-ticket__header">
      <img src="{{ Theme::asset()->url('images/inspira-logo-light.svg') }}" alt="Inspira" style="height:28px">
      <h2 class="booking-ticket__title">{{ $course?->name ?? __('Booking') }}</h2>
    </div>

    {{-- Bild links --}}
    <div class="booking-ticket__left">
      <img class="hero" src="{{ RvMedia::getImageUrl($course?->thumbnail, default: RvMedia::getDefaultImage()) }}" alt="{{ $course?->name }}">
    </div>

    {{-- Mitte: Kunde + Zeiten + Instructor + Chips --}}
    <div class="booking-ticket__middle booking-ticket__middle">
      {{-- Kunde --}}
      <div class="customer">
        <div class="subcap">{{ __('Vollständiger Name') }}</div>
        <div class="name-big">{{ $fullName }}</div>
        <div class="meta">
          @if($phone)<span>{{ $phone }}</span>@endif
          @if($email)<a href="mailto:{{ $email }}">{{ $email }}</a>@endif
        </div>
      </div>

      {{-- Zeiten --}}
      @if($startLabel24)
        <div class="field">
          <div class="val">{{ $startLabel24 }}</div>
          <small>{{ __('Startdatum der Sitzung') }}</small>
        </div>
      @endif
      @if($endLabel24)
        <div class="field">
          <div class="val">{{ $endLabel24 }}</div>
          <small>{{ __('Enddatum der Sitzung') }}</small>
        </div>
      @endif

      {{-- Instructor (ohne Box) --}}
      <div class="instructor">
        @if($instructor?->name)
          <div>
            <div class="lbl">{{ __('Instructor Name') }}</div>
            <div class="txt">{{ $instructor->name }}</div>
          </div>
        @endif
        @if($instructor?->email)
          <div>
            <div class="lbl">{{ __('Instructor Email') }}</div>
            <div class="txt"><a href="mailto:{{ $instructor->email }}">{{ $instructor->email }}</a></div>
          </div>
        @endif
      </div>

      {{-- Chips --}}
      <div class="chips">
        @if($course?->duration)
          <span class="chip">{{ __('Dauer:') }} {{ $course->duration }}</span>
        @endif
        @if($course?->category)
          <span class="chip">{{ $course->category->name }}</span>
        @endif
        @if($course?->price)
          <span class="chip">{{ format_price($course->price) }}</span>
        @endif
      </div>
    </div>

    {{-- Rechts: Preis/Status (z-index ganz hinten) --}}
    <div class="booking-ticket__right booking-ticket__right">
      <div class="right-title">{{ __('Gesamtpreis') }}</div>
      <div class="kv"><span>{{ __('Preis') }}</span>   <b>{{ format_price($booking->sub_total) }}</b></div>
      <div class="kv"><span>{{ __('Rabatt') }}</span>  <b>{{ format_price($booking->coupon_amount) }}</b></div>
      <div class="kv"><span>{{ __('Steuern') }}</span> <b>{{ format_price($booking->tax_amount) }}</b></div>
      <div class="divider"></div>
      <div class="total"><span>{{ __('Gesamt') }}</span><span>{{ format_price($booking->amount) }}</span></div>
      <div class="status-wrap">{!! $booking->status->toHtml() !!}</div>
    </div>

  </div>
</div>
