@if (is_plugin_active('payment'))
    <link rel="stylesheet" href="{{ asset('vendor/core/plugins/payment/css/payment.css') }}?v=1.0.6">
    @php
        Theme::asset()->container('header')->usePath()->add('jquery', 'plugins/jquery.min.js');
        Theme::asset()->container('header')->add('payment-js', 'vendor/core/plugins/payment/js/payment.js');
    @endphp
    {!! apply_filters(PAYMENT_FILTER_HEADER_ASSETS, null) !!}
@endif

@php
    Theme::set('pageTitle', '');
    Theme::asset()->container('footer')->usePath()->add('checkout-core', 'js/checkout-core.js');
    Theme::asset()->container('footer')->usePath()->add('checkout-commerce', 'js/checkout-commerce.js', ['jquery']);
    $startLabel24 = BaseHelper::formatDate($session->start_date, 'd.m.Y H:i');
    $endLabel24   = $session->end_date ? BaseHelper::formatDate($session->end_date, 'd.m.Y H:i') : null;
@endphp

<style>
:root{--mint:#578E88;--gray:#E5E7EB;}
.header,.topbar,.page-title,.breadcrumb,.page-breadcrumb,.hero-banner{display:none!important;}
.checkout-fw .container{max-width:980px;}
body .pt-120{padding-top:24px!important;}
body .pb-40{padding-bottom:24px!important;}
section.checkout-booking-page{background:#fff;}
.ticket{display:grid;grid-template-columns:1.1fr 1.4fr 1fr;border-radius:10px;overflow:hidden;background:#fff;box-shadow:0 2px 12px rgba(0,0,0,0.05);margin-bottom:28px;}
.ticket__col{padding:20px 22px;display:flex;flex-direction:column;justify-content:center;}
.ticket__col.ticket__media{padding:0;}
.ticket__media img{width:100%;height:100%;min-height:220px;object-fit:cover;}
.ticket__details{background:var(--mint);color:#fff;}
.ticket__details .title,.ticket__totals .title{font-weight:600;font-size:15px;margin-bottom:10px;}
.ticket__details .kv,.ticket__totals .kv{display:flex;justify-content:space-between;font-size:13px;line-height:1.6;margin:5px 0;}
.ticket__totals{background:#000;color:#fff;border-left:2px solid rgba(255,255,255,0.08);}
.ticket__totals hr{border:none;height:1px;background:rgba(255,255,255,0.15);margin:10px 0;}
.ticket__totals .total{font-size:16px;font-weight:700;}
@media(max-width:768px){
  .ticket{grid-template-columns:1fr;border-radius:12px;}
  .ticket__media img{min-height:180px;}
  .ticket__details,.ticket__totals{padding:18px 20px;}
  .ticket__totals{border-left:none;border-top:1px solid rgba(255,255,255,0.15);}
}
.stepper-wrap{text-align:center;margin-bottom:18px;}
#stepTitle{font-size:16px;font-weight:600;color:#3b4a47;margin-bottom:14px;}
.stepper{display:flex;align-items:center;justify-content:space-between;width:100%;}
.stepper .line{flex:1;height:2px;background:#E5E7EB;}
.stepper .dot{width:13px;height:13px;border-radius:50%;background:#D5D8D7;transition:all .3s ease;box-shadow:0 0 0 3px #fff inset;}
.stepper .dot.active{background:var(--mint);box-shadow:0 0 0 4px rgba(87,142,136,0.18);}
.stepper .line.active{background:var(--mint);}
.step-panel{display:none;padding:30px 26px 24px;background:#fff;border:none;}
.step-panel.active{display:block;animation:fade .2s ease-out;}
@keyframes fade{from{opacity:.4;transform:translateY(3px);}to{opacity:1;transform:none;}}
.form-control{height:46px;border-radius:6px;}
textarea.form-control{min-height:100px;}
.is-invalid{border-color:#c0392b!important;}
.form-alert{display:none;margin:0 0 16px;padding:12px 16px;border-radius:6px;background:#fff3f3;color:#b71c1c;font-size:14px;border:1px solid #f1b4b4;}
.coupon-wrapper{background:#F9F9F9;border:1px solid var(--gray);border-radius:8px;padding:20px;margin-bottom:24px;}
.list_payment_method{border:1px solid var(--gray);border-radius:8px;margin-bottom:20px;}
.list_payment_method li{padding:14px 16px;border-bottom:1px solid #f0f0f0;}
.list_payment_method li:last-child{border-bottom:none;}
.btnrow{display:flex;justify-content:center;gap:14px;margin-top:24px;}
.btnX{height:48px;font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;border-radius:3px;transition:all .25s ease;}
.btn-outline-mint{width:150px;border:1px solid var(--mint);color:var(--mint);background:#fff;}
.btn-outline-mint:hover{background:var(--mint);color:#fff;}
.btn-mint{flex:1;max-width:82%;background:var(--mint);color:#fff;border:1px solid var(--mint);}
.btn-mint:hover{filter:brightness(0.95);}
@media(max-width:768px){
  .btnrow{flex-direction:column-reverse;gap:10px;}
  .btnX{width:100%;max-width:100%;padding:14px 0;}
  .btn-mint{order:1;}
  .btn-outline-mint{order:2;}
}
.cxl-accordion details{border-radius:10px;background:#fff;border:1px solid var(--gray);margin-top:20px;}
.cxl-accordion summary{cursor:pointer;padding:14px 18px;font-weight:600;list-style:none;}
.cxl-accordion .cxl-body{padding:0 18px 18px;}
</style>

<section class="checkout-booking-page checkout-fw">
  <div class="container pt-120 pb-40 checkout-booking">

    {{-- ░░ Ticket Header ░░ --}}
    <div class="ticket">
      <div class="ticket__col ticket__media">
        <img src="{{ RvMedia::getImageUrl($course->thumbnail, default: RvMedia::getDefaultImage()) }}" alt="{{ $course->name }}">
      </div>
      <div class="ticket__col ticket__details">
        <h5 class="title">Ihre Reservierung</h5>
        <div class="kv"><span>{{ $course->name }}</span></div>
        <div class="kv"><span>Startdatum</span><b>{{ $startLabel24 }}</b></div>
        @if($endLabel24)
          <div class="kv"><span>Enddatum</span><b>{{ $endLabel24 }}</b></div>
        @endif
      </div>
      <div class="ticket__col ticket__totals">
        <h5 class="title">Gesamtpreis</h5>
        <div class="kv"><span>Preis</span><b class="amount-text">{{ format_price($amountNet) }}</b></div>
        <div class="kv"><span>Rabatt (Coupon)</span><b class="discount-text">{{ format_price($couponAmount) }}</b></div>
        <div class="kv"><span>Steuern</span><b class="tax-text">{{ format_price($taxAmount) }}</b></div>
        <hr>
        <div class="kv total"><span>Gesamt</span><b class="total-amount-text">{{ format_price($total) }}</b></div>
      </div>
    </div>

    {{-- ░░ Stepper ░░ --}}
    <div class="stepper-wrap">
      <div id="stepTitle">Allgemeine Informationen</div>
      <div class="stepper">
        <div class="dot active" data-step-dot="1"></div>
        <div class="line" data-step-line="1"></div>
        <div class="dot" data-step-dot="2"></div>
        <div class="line" data-step-line="2"></div>
        <div class="dot" data-step-dot="3"></div>
      </div>
    </div>

   
    {{-- ░░ Formular ░░ --}}
    <form action="{{ route('public.course.booking.checkout') }}" method="POST" id="bookingForm" class="payment-checkout-form" data-start-step="{{ $customer->id ? 2 : 1 }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <input type="hidden" name="amount" value="{{ $total }}">
      <input type="hidden" name="course_id" value="{{ $course->id }}">
      <input type="hidden" name="session_id" value="{{ $session->id }}">
      <input type="hidden" name="currency" value="{{ strtoupper(get_application_currency()->title) }}">
      <input type="hidden" name="currency_id" value="{{ get_application_currency_id() }}">

      {{-- Step 1 --}}
      <div class="step-panel active" data-step="1">
        @if ($customer->id)
          <p>Angemeldet als <b>{{ $customer->name ?? $customer->email }}</b></p>
          <div class="btnrow">
            <a href="{{ url()->previous() }}" class="btnX btn-outline-mint">Abbrechen</a>
            <button type="button" class="btnX btn-mint" data-next>Weiter</button>
          </div>
        @else
          <p>Wie möchtest du fortfahren?</p>
          <div class="btnrow">
            <button type="button" class="btnX btn-mint" data-next>Als Gast buchen</button>
            <a href="{{ route('customer.login') }}?redirect={{ urlencode(request()->fullUrl()) }}" class="btnX btn-outline-mint">Einloggen</a>
          </div>
        @endif
      </div>

      {{-- Step 2 --}}
      <div class="step-panel" data-step="2">
        <div id="formAlertStep2" class="form-alert"></div>
        <p>Pflichtfelder sind mit * gekennzeichnet</p>
        <div class="row">
          <div class="col-md-6 mb-3"><label>Vorname *</label><input id="txt-first_name" name="first_name" class="form-control" required></div>
          <div class="col-md-6 mb-3"><label>Nachname *</label><input id="txt-last_name" name="last_name" class="form-control" required></div>
          <div class="col-md-6 mb-3"><label>E-Mail *</label><input id="txt-email" name="email" class="form-control" type="email" required></div>
          <div class="col-md-6 mb-3"><label>Telefon *</label><input id="txt-phone" name="phone" class="form-control" required></div>
          <div class="col-md-6 mb-3"><label>Land</label><input id="txt-country" name="country" class="form-control"></div>
          <div class="col-md-6 mb-3"><label>Bundesland / Provinz</label><input id="txt-state" name="state" class="form-control"></div>
          <div class="col-md-6 mb-3"><label>Stadt</label><input id="txt-city" name="city" class="form-control"></div>
          <div class="col-md-6 mb-3"><label>Adresse</label><input id="txt-address" name="address" class="form-control"></div>
          <div class="col-md-6 mb-3"><label>Postleitzahl</label><input id="txt-zip" name="zip" class="form-control"></div>
        </div>
        <div class="mb-3"><label>Anfragen</label><textarea id="requests" name="requests" class="form-control" placeholder="Schreiben Sie etwas..."></textarea></div>
        <div class="btnrow">
          <button type="button" class="btnX btn-outline-mint" data-prev>Abbrechen</button>
          <button type="button" class="btnX btn-mint" data-next>Weiter</button>
        </div>
      </div>

      {{-- Step 3 --}}
      <div class="step-panel" data-step="3">
        <div id="formAlertStep3" class="form-alert"></div>
        <div class="coupon-wrapper" id="couponBox">@include('plugins/courses::coupons.partials.form')</div>
        <label>Zahlungsmethode</label>
        <ul class="list-group list_payment_method">
          {!! apply_filters(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, null, [
              'amount' => $total,
              'currency' => strtoupper(get_application_currency()->title),
              'name' => $course->name,
              'selected' => PaymentMethods::getSelectedMethod(),
              'default' => PaymentMethods::getDefaultMethod(),
              'selecting' => PaymentMethods::getSelectingMethod(),
          ]) !!}
          {!! PaymentMethods::render() !!}
        </ul>
        <label><input type="checkbox" id="terms_conditions" name="terms_conditions" value="1"> Allgemeine Geschäftsbedingungen *</label>
        <div class="btnrow">
          <button type="button" class="btnX btn-outline-mint" data-prev>Abbrechen</button>
          <button type="submit" class="btnX btn-mint payment-checkout-btn" data-processing-text="Wird verarbeitet..." data-error-header="Fehler">Abschließen</button>
        </div>
      </div>
    </form>

    @if ($cancellation = theme_option('cancellation'))
      <div class="cxl-accordion">
        <details>
          <summary>Stornierungsbedingungen</summary>
          <div class="cxl-body">{!! BaseHelper::clean($cancellation) !!}</div>
        </details>
      </div>
    @endif
  </div>
</section>

@if (is_plugin_active('payment'))
  {!! apply_filters(PAYMENT_FILTER_FOOTER_ASSETS, null) !!}
@endif

