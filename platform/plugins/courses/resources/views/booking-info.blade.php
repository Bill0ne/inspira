@php
    $booking  = $booking ?? null;
    $course   = $booking?->course;
    $session  = $booking?->session;

    if ($booking?->customer && $booking->customer->id) {
        $fullName = trim($booking->customer->first_name.' '.$booking->customer->last_name);
        $email    = $booking->customer->email;
        $phone    = $booking->customer->phone;
    } elseif ($booking?->address) {
        $fullName = trim($booking->address->first_name.' '.$booking->address->last_name);
        $email    = $booking->address->email;
        $phone    = $booking->address->phone;
    } else {
        $fullName = __('Gast');
        $email = $phone = null;
    }

    $startLabel24 = $session ? BaseHelper::formatDate($session->start_date, 'd.m.Y H:i') : null;
    $endLabel24   = ($session && $session->end_date) ? BaseHelper::formatDate($session->end_date, 'd.m.Y H:i') : null;
    $endTimeLabel = ($session && $session->end_date) ? BaseHelper::formatDate($session->end_date, 'H:i') : null;

    $baseNet = course_truncate_price(($booking->sub_total ?? 0) + ($booking->rule_discount ?? 0));
    $calculatedNet = course_truncate_price($booking->sub_total ?? 0);
    $netAfterCoupon = course_truncate_price(max($calculatedNet - ($booking->coupon_amount ?? 0), 0));
    $grossBeforeCard = course_truncate_price($netAfterCoupon + ($booking->tax_amount ?? 0));
    $cardDiscountGross = course_truncate_price($booking->customer_card_discount ?? 0);
    $amountBeforeFee = course_truncate_price(max($grossBeforeCard - $cardDiscountGross, 0));
    $minimumOnlinePaymentFee = course_truncate_price(max(0, ($booking->amount ?? 0) - $amountBeforeFee));
    $cardUnitsUsed = (int) ($booking->customer_card_units_used ?? 0);
    $cardUnitPrice = $cardUnitsUsed > 0 ? course_truncate_price($cardDiscountGross / $cardUnitsUsed) : 0;
    $configuratorNet = course_truncate_price($calculatedNet - $baseNet);
    $showConfiguratorRow = abs($configuratorNet) > 0.00001;

    $instructor = $course?->instructor;
@endphp

<style>
.booking-ticket-scope {
  --mint:#578E88;
  --chip-bg:#F3F3F3;
  --chip-fg:#578E88;
  --hole:20px;
}

.booking-ticket {
  display:grid;
  grid-template-columns:1fr 1fr 280px;
  background:#fff;
  border-radius:14px;
  overflow:hidden;
  box-shadow:0 6px 24px rgba(0,0,0,.06);
  position:relative;
  margin-bottom:32px;
}

/* HEADER */
.booking-ticket__header {
  position:absolute;
  top:0;
  left:0;
  width:calc(100% - 280px);
  height:60px;
  background:var(--mint);
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:0 24px;
  z-index:2;
}
.booking-ticket__header img {
  height:24px;
}
.booking-ticket__header h2 {
  color:#fff;
  font:600 16px/1.3 system-ui;
  margin:0;
}

/* BILD */
.booking-ticket__left img {
  width:100%;
  height:100%;
  min-height:240px;
  object-fit:cover;
  display:block;
  border-bottom-left-radius:0;
}

/* MITTELBEREICH */
.booking-ticket__middle {
  background:#F8FAFB;
  padding:80px 28px 32px;
  position:relative;
}
.booking-ticket .subcap {
  font:700 11px/1 system-ui;
  color:#9CA3AF;
  text-transform:uppercase;
}
.booking-ticket .name-big {
  font:600 15px/20px system-ui;
  color:#0F172A;
  margin:5px 0 18px;
}
.booking-ticket .field {
  margin:8px 0 18px;
}
.booking-ticket .field .val {
  font:600 15px/21px system-ui;
  color:#0F172A;
}
.booking-ticket .field small {
  display:block;
  margin-top:4px;
  font:400 12px/15px system-ui;
  color:#94A3B8;
}

/* CHIPS */
.booking-ticket .chips {
  display:flex;
  flex-wrap:wrap;
  gap:8px;
  margin-top:8px;
}
.booking-ticket .chip {
  background:var(--chip-bg);
  color:var(--chip-fg);
  font:500 12px/14px system-ui;
  padding:8px 14px;
  border-radius:0;
  display:inline-flex;
  align-items:center;
  gap:6px;
}

/* TRENNLINIE */
.booking-ticket__divider {
  position:absolute;
  top:0;
  bottom:0;
  right:280px;
  width:1px;
  border-left:2px dashed rgba(0,0,0,0.1);
  z-index:1;
}

/* PREISBEREICH */
.booking-ticket__right {
  background:#000;
  color:#fff;
  padding:0;
  position:relative;
  border-top-right-radius:14px;
  border-bottom-right-radius:14px;
  display:flex;
  flex-direction:column;
  justify-content:space-between;
}

/* STATUS-BANNER */
.booking-ticket__status {
  background:rgba(255,255,255,0.1);
  text-align:center;
  font:600 13px/1.4 system-ui;
  text-transform:uppercase;
  letter-spacing:0.05em;
  padding:12px 0;
  border-bottom:1px solid rgba(255,255,255,0.15);
}

/* INHALT RECHTS */
.booking-ticket__right-content {
  padding:32px 28px;
}
.booking-ticket .right-title {
  margin:0 0 14px;
  font:700 16px/21px system-ui;
}
.booking-ticket .kv {
  display:flex;
  justify-content:space-between;
  margin:6px 0;
}
.booking-ticket .kv span {
  color:#C7CED6;
  font:500 12px/15px system-ui;
}
.booking-ticket .kv b {
  color:#fff;
  font:600 13px/17px system-ui;
}
.booking-ticket .divider {
  height:1px;
  background:rgba(255,255,255,.15);
  margin:16px 0;
}
.booking-ticket .total {
  display:flex;
  justify-content:space-between;
  font:700 17px/21px system-ui;
  color:#fff;
}
.booking-ticket .kv b.negative {
  color:#F87171;
}
.booking-ticket .kv small {
  color:#C7CED6;
  font:500 11px/14px system-ui;
}

/* Löcher ganz oben & unten */
.booking-ticket__right::before,
.booking-ticket__right::after {
  content:"";
  position:absolute;
  left:-8px;
  width:var(--hole);
  height:var(--hole);
  border-radius:50%;
  background:#fff;
  box-shadow:0 0 0 1px rgba(0,0,0,.08) inset;
}
.booking-ticket__right::before { top:0; transform:translateY(-50%); }
.booking-ticket__right::after { bottom:0; transform:translateY(50%); }

/* RESPONSIVE */
@media(max-width:900px){
  .booking-ticket{
    grid-template-columns:1fr;
  }
  .booking-ticket__header{ width:100%; }
  .booking-ticket__divider{ display:none; }
  .booking-ticket__right::before,
  .booking-ticket__right::after{ display:none; }
}
</style>

<div class="booking-ticket-scope">
  <div class="booking-ticket">

    {{-- HEADER --}}
    <div class="booking-ticket__header">
      <img src="{{ Theme::asset()->url('images/inspira-logo-light.svg') }}" alt="Inspira">
      <h2>{{ $course?->name ?? __('Buchung') }}</h2>
    </div>

    {{-- BILD --}}
    <div class="booking-ticket__left">
      <img src="{{ RvMedia::getImageUrl($course?->thumbnail, default: RvMedia::getDefaultImage()) }}" alt="{{ $course?->name }}">
    </div>

    {{-- MITTELBEREICH --}}
    <div class="booking-ticket__middle">
      <div class="subcap">{{ __('Vollständiger Name') }}</div>
      <div class="name-big">{{ $fullName }}</div>

      @if($startLabel24)
        <div class="field session-field">
          <div class="val">{{ $startLabel24 }}@if($endTimeLabel) - {{ $endTimeLabel }}@endif</div>
          <small>{{ __('Sitzung') }}</small>
        </div>
      @endif

      <div class="chips">
        @if($course?->duration)
          <span class="chip"><i class="fal fa-clock"></i> {{ $course->duration }}</span>
        @endif
        @if($course?->category)
          <span class="chip">{{ $course->category->name }}</span>
        @endif
        @if($instructor?->name)
          <span class="chip">{{ __('By Coach:') }} {{ $instructor->name }}</span>
        @endif
      </div>

      <div class="field" style="margin-top:14px">
        <small>Inspira Zentrum<br>Max-Lang-Str. 36, 70771 Leinfelden-Echterdingen</small>
      </div>
    </div>

    {{-- LINIE --}}
    <div class="booking-ticket__divider"></div>

    {{-- PREIS & STATUS --}}
    <div class="booking-ticket__right">
      <div class="booking-ticket__status">
        @php
            $paymentStatus = ($booking->payment && $booking->payment->id) ? $booking->payment->status : null;
        @endphp

        {!! $paymentStatus?->toHtml() ?? $booking->status->toHtml() !!}
      </div>
      <div class="booking-ticket__right-content">
        <div class="right-title">{{ __('Gesamtpreis') }}</div>
        <div class="kv"><span>{{ __('Originalpreis') }}</span><b>{{ course_format_price($baseNet) }}</b></div>
        <div class="kv @if (! $showConfiguratorRow) d-none @endif">
          <span>{{ __('Preis Konfigurator') }}</span>
          @php($configuratorPrefix = $configuratorNet > 0 ? '+' : ($configuratorNet < 0 ? '-' : ''))
          <b>{{ $configuratorPrefix }}{{ course_format_price(abs($configuratorNet)) }}</b>
        </div>
        <div class="kv"><span>{{ __('Steuern') }}</span><b>{{ course_format_price($booking->tax_amount) }}</b></div>
        <div class="kv"><span>{{ __('Bruttopreis') }}</span><b>{{ course_format_price($grossBeforeCard) }}</b></div>
        <div class="divider"></div>
        <div class="kv"><span>{{ __('Rabatt (Coupon)') }}</span><b class="negative">{{ $booking->coupon_amount > 0 ? '-' : '' }}{{ course_format_price($booking->coupon_amount) }}</b></div>
        @if($cardDiscountGross > 0)
          <div class="kv"><span>{{ __('Kartenrabatt') }}</span><b class="negative">-{{ course_format_price($cardDiscountGross) }}</b></div>
          @if($cardUnitPrice > 0)
            <div class="kv"><span>{{ __('Rabatt je Einheit') }}</span><b>{{ course_format_price($cardUnitPrice) }}</b></div>
            <div class="kv"><span>{{ __('Verbrauchte Einheiten') }}</span><b>{{ $cardUnitsUsed }}</b></div>
          @endif
        @endif
        @if($minimumOnlinePaymentFee > 0)
          <div class="kv"><span>{{ __('Mindestgebühr (Online-Zahlung)') }}</span><b>{{ course_format_price($minimumOnlinePaymentFee) }}</b></div>
        @endif
        <div class="divider"></div>
        <div class="total"><span>{{ __('Gesamt') }}</span><span>{{ course_format_price($booking->amount) }}</span></div>
      </div>
    </div>

  </div>
</div>
