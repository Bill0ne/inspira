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
    Theme::set('breadcrumb', false);
    Theme::asset()->container('footer')->usePath()->add('checkout-core', 'js/checkout-core.js');
    Theme::asset()->container('footer')->usePath()->add('checkout-commerce', 'js/checkout-commerce.js', ['jquery']);
    Theme::asset()->container('footer')->add('customer-card-js', asset('vendor/core/plugins/hotel/js/customer-card.js'), ['jquery']);
    $startLabel24 = BaseHelper::formatDate($session->start_date, 'd.m.Y H:i');
    $endLabel24   = $session->end_date ? BaseHelper::formatDate($session->end_date, 'd.m.Y H:i') : null;

    session(['url.intended' => request()->fullUrl()]);
    $shouldStartRegister = old('register_customer') == 1;
    $availableCards = $availableCards ?? collect();
    $selectedCard = $selectedCard ?? null;
    $cardDiscount = $cardDiscount ?? 0;
    $totalAfterDiscount = $totalAfterDiscount ?? $total;
@endphp

@include('plugins/hotel::customer-cards.partials.scripts', ['jsValidator' => null])

<style>
:root{--mint:#578E88;--gray:#E5E7EB;--ink:#17463F;}
body > header.header-area,
body .header,
body .header-top,
body .menu-area,
body #header-sticky,
body .topbar,
body .page-title,
body .breadcrumb,
body .breadcrumb-area,
body .page-breadcrumb,
body .hero-banner,
body .second-header,
body .main-menu{display:none!important;}
.checkout-fw .container{max-width:980px;}
body .pt-120{padding-top:24px!important;}
body .pb-40{padding-bottom:24px!important;}
section.checkout-booking-page{background:#fff;}
.checkout-topbar{position:sticky;top:0;z-index:40;display:flex;align-items:center;gap:10px;padding:10px 24px;background:#F8F8F8;color:var(--ink);font-size:14px;font-weight:600;box-shadow:0 1px 0 rgba(23,70,63,0.06);}
.checkout-topbar__link{display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:50%;border:1px solid rgba(23,70,63,0.15);color:var(--ink);transition:all .2s ease;}
.checkout-topbar__link svg{width:14px;height:14px;}
.checkout-topbar__link:hover{background:rgba(23,70,63,0.08);}
.checkout-modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(18,33,31,0.55);z-index:99;padding:24px;}
.checkout-modal__backdrop{position:absolute;inset:0;}
.checkout-modal.is-visible{display:flex;}
.checkout-modal__dialog{background:#fff;border-radius:12px;max-width:420px;width:100%;padding:30px 28px;position:relative;box-shadow:0 20px 50px rgba(0,0,0,0.15);z-index:1;}
.checkout-modal__close{position:absolute;top:16px;right:16px;background:none;border:none;font-size:20px;color:#4b5c58;cursor:pointer;line-height:1;}
.checkout-modal__title{font-weight:600;font-size:18px;margin-bottom:18px;color:var(--ink);}
.checkout-modal__form .form-control{margin-bottom:14px;height:46px;border-radius:6px;}
.checkout-modal__actions{display:flex;align-items:center;justify-content:space-between;margin-top:10px;}
.checkout-modal__actions label{font-size:13px;}
.checkout-modal__actions a{font-size:13px;color:var(--mint);font-weight:600;}
.checkout-modal__submit{width:100%;margin-top:6px;background:var(--mint);border:1px solid var(--mint);color:#fff;font-weight:600;height:46px;border-radius:6px;transition:filter .2s ease;}
.checkout-modal__submit:hover{filter:brightness(0.95);}
.checkout-modal__footer{text-align:center;font-size:13px;margin-top:16px;color:#4b5c58;}
.checkout-modal__footer a{color:var(--mint);font-weight:600;}
.checkout-modal-open{overflow:hidden;}
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
.register-box{margin-top:18px;padding:20px 24px;border:1px solid var(--gray);border-radius:10px;background:#fbfbfb;box-shadow:0 8px 22px rgba(0,0,0,0.05);}
.register-box .btnrow{justify-content:flex-end;}
.register-box .form-control{height:44px;}
.form-alert{display:none;margin:0 0 16px;padding:12px 16px;border-radius:6px;background:#fff3f3;color:#b71c1c;font-size:14px;border:1px solid #f1b4b4;}
.coupon-wrapper{padding:0;margin-bottom:24px;border:none;background:none;}
.checkout-action-card{padding:24px;border-radius:18px;border:1px solid rgba(23,70,63,0.12);background:linear-gradient(180deg,#fff 0%,#f7fbfa 100%);transition:box-shadow .25s ease,transform .25s ease;}
.checkout-action-card:hover{transform:translateY(-2px);}
.checkout-action-card__header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;}
.checkout-action-card__eyebrow{display:block;font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:rgba(23,70,63,0.65);margin-bottom:6px;}
.checkout-action-card__title{margin:0;font-size:20px;font-weight:600;color:var(--ink);}
.checkout-action-toggle{display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:999px;background:rgba(87,142,136,0.12);color:var(--mint);font-weight:600;border:none;cursor:pointer;transition:background .2s ease,transform .2s ease;}
.checkout-action-toggle:hover{background:rgba(87,142,136,0.2);transform:translateY(-1px);}
.checkout-action-form{margin-top:20px;display:flex;flex-direction:column;gap:14px;}
.checkout-action-label{font-weight:600;color:var(--ink);margin-bottom:0;}
.checkout-action-controls{display:grid;grid-template-columns:minmax(0,1fr) auto auto;gap:12px;align-items:center;}
.checkout-action-controls--single{grid-template-columns:minmax(0,1fr) auto;}
.checkout-action-input,
.checkout-action-select{height:50px;border-radius:14px;border:1px solid rgba(23,70,63,0.18);background:#fff;padding:0 18px;font-weight:500;color:var(--ink);transition:border-color .2s ease,box-shadow .2s ease;}
.checkout-action-input:focus,
.checkout-action-select:focus{border-color:var(--mint);box-shadow:0 0 0 4px rgba(87,142,136,0.18);outline:none;}
.checkout-action-button{height:50px;border-radius:14px;padding:0 24px;font-weight:600;font-size:14px;display:inline-flex;align-items:center;justify-content:center;gap:8px;transition:transform .2s ease,box-shadow .2s ease,background .2s ease;color:var(--ink);border:1px solid transparent;}
.checkout-action-button--primary{background:var(--mint);color:#fff;border-color:var(--mint);}
.checkout-action-button--primary:hover{transform:translateY(-1px);}
.checkout-action-button--ghost{background:rgba(87,142,136,0.08);color:var(--mint);}
.checkout-action-button--ghost:hover{background:rgba(87,142,136,0.16);}
.checkout-action-feedback{margin-top:18px;border-radius:14px;padding:16px 20px;background:rgba(87,142,136,0.12);border:1px solid transparent;color:var(--ink);display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:12px;}
.checkout-action-feedback strong{font-size:16px;color:var(--mint);}
.checkout-action-feedback .checkout-action-button{height:44px;padding:0 18px;}
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
.checkout-action-card__header,.checkout-action-form{width:100%;}
@media(max-width:992px){
  .checkout-action-controls{grid-template-columns:1fr auto auto;}
}
@media(max-width:768px){
  .checkout-action-controls,.checkout-action-controls--single{grid-template-columns:1fr;}
  .checkout-action-button{width:100%;}
  .checkout-action-toggle{width:100%;justify-content:center;}
  .checkout-action-card{padding:20px;}
  .checkout-action-feedback{flex-direction:column;align-items:stretch;}
  .checkout-action-feedback .checkout-action-button{width:100%;}
}
@media(max-width:480px){
  .checkout-action-card__title{font-size:18px;}
  .checkout-action-card{border-radius:16px;}
}
.cxl-accordion details{border-radius:10px;background:#fff;border:1px solid var(--gray);margin-top:20px;}
.cxl-accordion summary{cursor:pointer;padding:14px 18px;font-weight:600;list-style:none;}
.cxl-accordion .cxl-body{padding:0 18px 18px;}
</style>

<div class="checkout-topbar">
  <a class="checkout-topbar__link" href="{{ route('public.courses') }}" aria-label="Zurück zur Übersicht">
    <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M9.5 3.5 5 8l4.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </a>
  <span>Zurück zur Übersicht</span>
</div>

@php
  $contactValues = [
      'first_name' => old('first_name', data_get($checkoutData ?? [], 'first_name', optional($customer)->first_name)),
      'last_name' => old('last_name', data_get($checkoutData ?? [], 'last_name', optional($customer)->last_name)),
      'email' => old('email', data_get($checkoutData ?? [], 'email', optional($customer)->email)),
      'phone' => old('phone', data_get($checkoutData ?? [], 'phone', optional($customer)->phone)),
      'country' => old('country', data_get($checkoutData ?? [], 'country', optional($customer)->country)),
      'state' => old('state', data_get($checkoutData ?? [], 'state', optional($customer)->state)),
      'city' => old('city', data_get($checkoutData ?? [], 'city', optional($customer)->city)),
      'address' => old('address', data_get($checkoutData ?? [], 'address', optional($customer)->address)),
      'zip' => old('zip', data_get($checkoutData ?? [], 'zip', optional($customer)->zip)),
  ];

  $prefillPayload = collect($contactValues)
      ->filter(fn ($value) => !blank($value))
      ->map(fn ($value) => is_string($value) ? $value : (string) $value)
      ->all();
@endphp

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
        @php
          $configuratorNet = $priceBreakdown['configurator_net'] ?? 0;
          $showConfiguratorRow = abs($configuratorNet) > 0.00001;
          $configuratorPrefix = $configuratorNet > 0 ? '+' : ($configuratorNet < 0 ? '-' : '');
        @endphp
        <div class="kv"><span>Originalpreis</span><b>{{ course_format_price($priceBreakdown['base_net'] ?? 0) }}</b></div>
        <div class="kv price-configurator-row {{ $showConfiguratorRow ? '' : 'd-none' }}">
          <span>Preis Konfigurator</span>
          <b class="configurator-text">
            @if($configuratorPrefix)
              {{ $configuratorPrefix }}{{ course_format_price(abs($configuratorNet)) }}
            @else
              {{ course_format_price(0) }}
            @endif
          </b>
        </div>
        <div class="kv"><span>Steuern</span><b class="tax-text">{{ course_format_price($priceBreakdown['calculated_tax'] ?? 0) }}</b></div>
        <div class="kv"><span>Bruttopreis</span><b class="amount-text">{{ course_format_price($priceBreakdown['calculated_gross'] ?? 0) }}</b></div>
        <div class="kv"><span>Rabatt (Coupon)</span><b class="discount-text">{{ $couponAmount > 0 ? '-' : '' }}{{ course_format_price($couponAmount) }}</b></div>
        <div class="kv card-discount-row {{ $cardDiscount > 0 ? '' : 'd-none' }}"><span>Kartenrabatt</span><b class="card-discount-text">-{{ course_format_price($cardDiscount) }}</b></div>
        <div class="kv minimum-fee-row {{ $minimumOnlinePaymentFee > 0 ? '' : 'd-none' }}"><span>Mindestgebühr (Online-Zahlung)</span><b class="minimum-fee-text">{{ course_format_price($minimumOnlinePaymentFee) }}</b></div>
        <hr>
        <div class="kv total"><span>Gesamt</span><b class="total-amount-text">{{ course_format_price($finalTotal) }}</b></div>
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
    <form action="{{ route('public.course.booking.checkout') }}" method="POST" id="bookingForm" class="payment-checkout-form" data-start-step="{{ $customer->id ? 2 : 1 }}" data-storage-key="course-checkout" data-start-register="{{ $shouldStartRegister ? '1' : '0' }}" data-prefill='@json($prefillPayload)'>
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <input
        type="hidden"
        name="amount"
        value="{{ number_format($finalTotal, 2, '.', '') }}"
        data-total
        data-original-total="{{ number_format($total, 2, '.', '') }}"
        data-active-discount="{{ number_format($cardDiscount, 2, '.', '') }}"
        data-minimum-fee="{{ number_format($minimumOnlinePaymentFee, 2, '.', '') }}"
        data-minimum-threshold="{{ number_format($minimumOnlinePaymentThreshold, 2, '.', '') }}"
      >
      <input type="hidden" name="course_id" value="{{ $course->id }}">
      <input type="hidden" name="session_id" value="{{ $session->id }}">
      <input type="hidden" name="currency" value="{{ strtoupper(get_application_currency()->title) }}">
      <input type="hidden" name="currency_id" value="{{ get_application_currency_id() }}">
      <input type="hidden" name="register_customer" value="{{ old('register_customer', 0) }}" id="register_customer_flag">
      <input type="hidden" name="customer_card_id" value="{{ $selectedCard?->getKey() }}" data-customer-card-input>

      {{-- Step 1 --}}
      <div class="step-panel active" data-step="1">
        <div id="formAlertStep1" class="form-alert"></div>
        @if ($customer->id)
          <p>Angemeldet als <b>{{ $customer->name ?? $customer->email }}</b></p>
          <div class="btnrow">
            <a href="{{ url()->previous() }}" class="btnX btn-outline-mint">Abbrechen</a>
            <button type="button" class="btnX btn-mint" data-next>Weiter</button>
          </div>
        @else
          <p>Wie möchtest du fortfahren?</p>
          <div class="btnrow" data-register-actions>
            <button type="button" class="btnX btn-mint" data-next>Als Gast buchen</button>
            <button type="button" class="btnX btn-outline-mint" data-open-login>Einloggen</button>
            <button type="button" class="btnX btn-outline-mint" data-toggle-register>Registrieren</button>
          </div>
          <div class="register-box {{ $shouldStartRegister ? '' : 'd-none' }}" data-register-box>
            <div class="row g-3">
              <div class="col-md-6">
                <label>Vorname *</label>
                <input type="text" name="register_first_name" class="form-control" value="{{ old('first_name') }}" autocomplete="given-name">
              </div>
              <div class="col-md-6">
                <label>Nachname *</label>
                <input type="text" name="register_last_name" class="form-control" value="{{ old('last_name') }}" autocomplete="family-name">
              </div>
              <div class="col-md-12">
                <label>E-Mail *</label>
                <input type="email" name="register_email" class="form-control" value="{{ old('email') }}" autocomplete="email">
              </div>
              <div class="col-md-6">
                <label>Passwort *</label>
                <input type="password" name="password" class="form-control" autocomplete="new-password">
              </div>
              <div class="col-md-6">
                <label>Passwort bestätigen *</label>
                <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
              </div>
            </div>
            <div class="btnrow mt-3">
              <button type="button" class="btnX btn-outline-mint" data-cancel-register>Abbrechen</button>
              <button type="button" class="btnX btn-mint" data-register-next>Weiter</button>
            </div>
          </div>
        @endif
      </div>

      {{-- Step 2 --}}
      <div class="step-panel" data-step="2">
        <div id="formAlertStep2" class="form-alert"></div>
        <p>Pflichtfelder sind mit * gekennzeichnet</p>
        <div class="row">
          <div class="col-md-6 mb-3"><label>Vorname *</label><input id="txt-first_name" name="first_name" class="form-control" value="{{ $contactValues['first_name'] }}" required></div>
          <div class="col-md-6 mb-3"><label>Nachname *</label><input id="txt-last_name" name="last_name" class="form-control" value="{{ $contactValues['last_name'] }}" required></div>
          <div class="col-md-6 mb-3"><label>E-Mail *</label><input id="txt-email" name="email" class="form-control" type="email" value="{{ $contactValues['email'] }}" required></div>
          <div class="col-md-6 mb-3"><label>Telefon *</label><input id="txt-phone" name="phone" class="form-control" value="{{ $contactValues['phone'] }}" required></div>
          <div class="col-md-6 mb-3"><label>Land</label><input id="txt-country" name="country" class="form-control" value="{{ $contactValues['country'] }}"></div>
          <div class="col-md-6 mb-3"><label>Bundesland / Provinz</label><input id="txt-state" name="state" class="form-control" value="{{ $contactValues['state'] }}"></div>
          <div class="col-md-6 mb-3"><label>Stadt</label><input id="txt-city" name="city" class="form-control" value="{{ $contactValues['city'] }}"></div>
          <div class="col-md-6 mb-3"><label>Adresse</label><input id="txt-address" name="address" class="form-control" value="{{ $contactValues['address'] }}"></div>
          <div class="col-md-6 mb-3"><label>Postleitzahl</label><input id="txt-zip" name="zip" class="form-control" value="{{ $contactValues['zip'] }}"></div>
        </div>
        <div class="mb-3"><label>Anfragen</label><textarea id="requests" name="requests" class="form-control" placeholder="Schreiben Sie etwas...">{{ old('requests') }}</textarea></div>
        <div class="btnrow">
          <button type="button" class="btnX btn-outline-mint" data-prev>Abbrechen</button>
          <button type="button" class="btnX btn-mint" data-next>Weiter</button>
        </div>
      </div>

      {{-- Step 3 --}}
      <div class="step-panel" data-step="3">
        <div id="formAlertStep3" class="form-alert"></div>
        @if ($availableCards->isNotEmpty())
          <div class="checkout-action-card mb-3">
            <div class="checkout-action-card__header">
              <div>
                <span class="checkout-action-card__eyebrow">Kundenkarte</span>
                <h5 class="checkout-action-card__title">Kundenkarte anwenden</h5>
              </div>
            </div>
            <div class="checkout-action-form">
              <label class="form-label checkout-action-label" for="customer_card_select">Kundenkarte auswählen</label>
              <div class="checkout-action-controls">
                <select id="customer_card_select" class="form-select checkout-action-select" data-course="{{ $course->id }}">
                  <option value="">Keine Karte auswählen</option>
                  @foreach ($availableCards as $card)
                    <option value="{{ $card->id }}" @selected($selectedCard && $selectedCard->id === $card->id)>
                      {{ $card->name }}@if ($card->uid) — {{ $card->uid }}@endif
                    </option>
                  @endforeach
                </select>
                <button class="checkout-action-button checkout-action-button--primary" type="button" data-bb-customer-card="apply">Anwenden</button>
                <button class="checkout-action-button checkout-action-button--ghost {{ $selectedCard ? '' : 'd-none' }}" data-bb-customer-card="remove" type="button">Entfernen</button>
              </div>
            </div>
            <div class="checkout-action-feedback {{ $cardDiscount > 0 ? '' : 'd-none' }}" data-bb-customer-card="info">
              <span>Kartenrabatt: <strong data-bb-customer-card="discount">{{ course_format_price($cardDiscount) }}</strong></span>
              <button class="checkout-action-button checkout-action-button--ghost {{ $selectedCard ? '' : 'd-none' }}" data-bb-customer-card="remove" type="button">Entfernen</button>
            </div>
          </div>
        @endif
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
        <label class="d-flex align-items-center gap-2">
          <input type="checkbox" id="terms_conditions" name="terms_conditions" value="1" @checked(old('terms_conditions'))>
          <span>
            Allgemeine&nbsp;Geschäftsbedingungen&nbsp;*
            <a href="https://stage.inspira-zentrum.de/de/term-and-conditions"
               target="_blank"
               rel="noopener"
               style="color:#578E88;font-weight:600;text-decoration:underline;">
              (AGB&nbsp;öffnen)
            </a>
          </span>
        </label>
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

{!! Theme::partial('checkout.login-modal', ['redirectUrl' => request()->fullUrl()]) !!}

@if (is_plugin_active('payment'))
  {!! apply_filters(PAYMENT_FILTER_FOOTER_ASSETS, null) !!}
@endif

