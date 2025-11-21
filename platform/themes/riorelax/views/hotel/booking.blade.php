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
    Theme::asset()
        ->container('footer')
        ->usePath()
        ->add('checkout-core', 'js/checkout-core.js', [], [], riorelax_theme_asset_version('js/checkout-core.js'));
    Theme::asset()
        ->container('footer')
        ->usePath()
        ->add(
            'checkout-hotel',
            'js/checkout-hotel.js',
            ['jquery'],
            [],
            riorelax_theme_asset_version('js/checkout-hotel.js')
        );

    if (is_plugin_active('payment')) {
        Theme::asset()
            ->container('footer')
            ->writeContent('payment-footer-assets', apply_filters(PAYMENT_FILTER_FOOTER_ASSETS, null));
    }

    Theme::asset()
        ->container('footer')
        ->add('js-validation', 'vendor/core/core/js-validation/js/js-validation.js', ['jquery'])
        ->writeContent('checkout-validator', JsValidator::formRequest(Botble\Hotel\Http\Requests\CheckoutRequest::class));

    $startLabel24 = $displayStart ? BaseHelper::formatDate($displayStart, 'd.m.Y H:i') : null;
    $endLabel24 = $displayEnd ? BaseHelper::formatDate($displayEnd, 'd.m.Y H:i') : null;
    $isLoggedIn = auth('customer')->check() || auth()->check();
    $canShowRoomPrices = HotelHelper::canShowRoomPrices();
    $priceInquiryText = HotelHelper::getRoomPriceInquiryText();
    $priceInquiryUrl = 'https://inspira-zentrum.net/de/nimm-kontakt-mit-uns-auf';

    $roomPriceDisplay = $totalRoomPrice ?? 0;
    $roomSubtotalBeforeDiscount = $roomSubtotalBeforeDiscount ?? ($roomPriceDisplay + ($mengenrabattAmount ?? 0));
    $extrasAmountDisplay = $extrasAmount ?? 0;
    session(['url.intended' => request()->fullUrl()]);
    $shouldStartRegister = old('register_customer') == 1;

    $requestsValue = old('requests', '');
    if ($requestsValue === "{{ old('requests') }}") {
        $requestsValue = '';
    }
    $isHotelCheckout = ! isset($course);
@endphp

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

/* ==== Topbar ==== */
.checkout-topbar{position:sticky;top:0;z-index:40;display:flex;align-items:center;gap:10px;padding:10px 24px;background:#F8F8F8;color:var(--ink);font-size:14px;font-weight:600;box-shadow:0 1px 0 rgba(23,70,63,0.06);}
.checkout-topbar__link{display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:50%;border:1px solid rgba(23,70,63,0.15);color:var(--ink);transition:all .2s ease;}
.checkout-topbar__link svg{width:14px;height:14px;}
.checkout-topbar__link:hover{background:rgba(23,70,63,0.08);}

/* ==== Modal ==== */
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

/* ==== Ticket Header ==== */
.ticket{display:grid;grid-template-columns:1.1fr 1.4fr 1fr;border-radius:10px;overflow:hidden;background:#fff;box-shadow:0 2px 12px rgba(0,0,0,0.05);margin-bottom:28px;}
.ticket__col{padding:20px 22px;display:flex;flex-direction:column;justify-content:center;gap:8px;}
.ticket__col.ticket__media{padding:0;}
.ticket__media img{width:100%;height:100%;min-height:220px;object-fit:cover;}
.ticket__details{background:var(--mint);color:#fff;}
.ticket__details .title,.ticket__totals .title{font-weight:600;font-size:15px;margin-bottom:10px;}
.ticket__details .kv,.ticket__totals .kv{display:flex;justify-content:space-between;font-size:13px;line-height:1.6;margin:3px 0;gap:12px;}
.ticket__details .kv span{font-weight:500;}
.ticket__details .kv b{color:#fff;font-weight:600;}
.ticket__totals{background:#000;color:#fff;border-left:2px solid rgba(255,255,255,0.08);}
.ticket__totals hr{border:none;height:1px;background:rgba(255,255,255,0.15);margin:10px 0;}
.ticket__totals .total{font-size:16px;font-weight:700;}
.ticket__totals .kv small{font-size:12px;color:rgba(255,255,255,0.7);}
@media(max-width:768px){
  .ticket{grid-template-columns:1fr;border-radius:12px;}
  .ticket__media img{min-height:180px;}
  .ticket__details,.ticket__totals{padding:18px 20px;}
  .ticket__totals{border-left:none;border-top:1px solid rgba(255,255,255,0.15);}
}

/* ==== Stepper ==== */
.stepper-wrap{text-align:center;margin-bottom:18px;}
#stepTitle{font-size:16px;font-weight:600;color:#3b4a47;margin-bottom:14px;}
.stepper{display:flex;align-items:center;justify-content:space-between;width:100%;max-width:320px;margin:0 auto;}
.stepper .line{flex:1;height:2px;background:#E5E7EB;}
.stepper .dot{width:13px;height:13px;border-radius:50%;background:#D5D8D7;transition:all .3s ease;box-shadow:0 0 0 3px #fff inset;}
.stepper .dot.active{background:var(--mint);box-shadow:0 0 0 4px rgba(87,142,136,0.18);}
.stepper .line.active{background:var(--mint);}
@media(max-width:768px){#stepTitle{font-size:15px;}}

/* ==== Panels ==== */
.step-panel{display:none;padding:30px 26px 24px;background:#fff;border:none;border-radius:10px;box-shadow:0 6px 18px rgba(0,0,0,0.06);}
.step-panel.active{display:block;animation:fade .2s ease-out;}
@keyframes fade{from{opacity:.4;transform:translateY(3px);}to{opacity:1;transform:none;}}
.form-control,.form-select{height:46px;border-radius:6px;}
textarea.form-control{min-height:100px;}
.is-invalid{border-color:#c0392b!important;}
.register-box{margin-top:18px;padding:20px 24px;border:1px solid var(--gray);border-radius:10px;background:#fbfbfb;box-shadow:0 8px 22px rgba(0,0,0,0.05);}
.register-box .btnrow{justify-content:flex-end;}
.register-box .form-control{height:44px;}
.step-section{margin-bottom:22px;}
.step-section h5{font-size:15px;font-weight:600;margin-bottom:12px;color:#17463f;}
.step-section p{margin-bottom:14px;}

/* ==== Alert Box ==== */
.form-alert{display:none;margin:0 0 16px;padding:12px 16px;border-radius:6px;background:#fff3f3;color:#b71c1c;font-size:14px;border:1px solid #f1b4b4;}


/* ==== Payment ==== */
.list_payment_method{border:1px solid var(--gray);border-radius:8px;margin-bottom:20px;overflow:hidden;}
.list_payment_method li{padding:14px 16px;border-bottom:1px solid #f0f0f0;}
.list_payment_method li:last-child{border-bottom:none;}

/* ==== Buttons ==== */
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

/* ==== Accordion ==== */
.cxl-accordion details{border-radius:10px;background:#fff;border:1px solid var(--gray);margin-top:20px;}
.cxl-accordion summary{cursor:pointer;padding:14px 18px;font-weight:600;list-style:none;}
.cxl-accordion .cxl-body{padding:0 18px 18px;}

/* ==== Addons ==== */
.addon-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;}
.addon-card{border:1px solid var(--gray);border-radius:8px;padding:14px 16px;display:flex;gap:10px;align-items:flex-start;background:#fff;transition:border-color .2s ease,box-shadow .2s ease;}
.addon-card input[type=checkbox]{margin-top:4px;}
.addon-card .addon-meta{flex:1;}
.addon-card .addon-name{font-weight:600;font-size:14px;margin-bottom:4px;color:#1f2f2b;}
.addon-card .addon-price{font-size:13px;color:#578E88;font-weight:600;}
.addon-card:hover{border-color:var(--mint);box-shadow:0 3px 10px rgba(0,0,0,0.06);}

.requests-box textarea{min-height:100px;}

.booking-facts{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-top:16px;font-size:13px;color:#3c4b47;}
.booking-facts div{background:#f7f9f8;border-radius:8px;padding:10px 12px;}
.booking-facts span{display:block;font-weight:600;color:#17463f;margin-bottom:6px;}

@media(max-width:575px){
  .step-panel{padding:24px 18px;}
}
</style>

<div class="checkout-topbar">
  <a class="checkout-topbar__link" href="{{ route('public.rooms') }}" aria-label="Zurück zur Übersicht">
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

@if ($isHotelCheckout)
<section class="checkout-booking-page checkout-fw" data-checkout-context="hotel">
  <div class="container pt-120 pb-40 checkout-booking">

    {{-- ░░ Ticket Header ░░ --}}
    <div class="ticket">
      <div class="ticket__col ticket__media">
        <img src="{{ RvMedia::getImageUrl($room->image, default: RvMedia::getDefaultImage()) }}" alt="{{ $room->name }}">
      </div>
      <div class="ticket__col ticket__details">
        <h5 class="title">Ihre Reservierung</h5>
        <div class="kv"><span>{{ $room->name }}</span></div>
        @if(!empty($slotSummaries))
          @foreach($slotSummaries as $index => $slot)
            @php
              $slotStart = BaseHelper::formatDate($slot['start_date'], 'd.m.Y H:i');
              $slotEnd = $slot['end_date'] ? BaseHelper::formatDate($slot['end_date'], 'd.m.Y H:i') : null;
            @endphp
            <div class="kv"><span>Zeitraum {{ $index + 1 }}</span><b>{{ $slotStart }} @if($slotEnd) – {{ $slotEnd }} @endif</b></div>
          @endforeach
        @else
          @if($startLabel24)
            <div class="kv"><span>Check-in</span><b>{{ $startLabel24 }}</b></div>
          @endif
          @if($endLabel24)
            <div class="kv"><span>Check-out</span><b>{{ $endLabel24 }}</b></div>
          @endif
        @endif
        <div class="kv"><span>Zimmer</span><b>{{ $rooms }}</b></div>
        <div class="kv"><span>Erwachsene</span><b>{{ $adults }}</b></div>
      </div>
      <div class="ticket__col ticket__totals">
        <h5 class="title">Gesamtpreis</h5>
        @php($hasQuantityDiscount = ($mengenrabattAmount ?? 0) > 0)
        <div class="kv room-subtotal-line @if(! $hasQuantityDiscount) d-none @endif">
          <span>Zwischensumme</span>
          <b class="room-subtotal-text">{!! $canShowRoomPrices ? format_price($roomSubtotalBeforeDiscount) : $priceInquiryText !!}</b>
        </div>
        <div class="kv mengenrabatt-line @if(! $hasQuantityDiscount) d-none @endif">
          <span>Mengenrabatt</span>
          @if($canShowRoomPrices)
            <b class="quantity-discount-text">{{ $hasQuantityDiscount ? '-' : '' }}{{ format_price($mengenrabattAmount ?? 0) }}</b>
          @else
            <b class="quantity-discount-text">{!! $priceInquiryText !!}</b>
          @endif
        </div>
        <div class="kv"><span>Preis</span><b class="amount-text">{!! $canShowRoomPrices ? format_price($roomPriceDisplay) : $priceInquiryText !!}</b></div>
        @if($extrasAmountDisplay > 0)
          <div class="kv"><span>Zusatzleistungen</span><b>{!! $canShowRoomPrices ? format_price($extrasAmountDisplay) : $priceInquiryText !!}</b></div>
        @endif
        <div class="kv"><span>Steuern</span><b class="tax-text">{!! $canShowRoomPrices ? format_price($taxAmount) : $priceInquiryText !!}</b></div>
        <hr>
        <div class="kv total"><span>Gesamt</span><b class="total-amount-text">{!! $canShowRoomPrices ? format_price($total) : $priceInquiryText !!}</b></div>
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
    <form
      action="{{ route('public.booking.checkout') }}"
      method="POST"
      id="bookingForm"
      class="payment-checkout-form"
      data-checkout-context="hotel"
      data-start-step="{{ $customer->id ? 2 : 1 }}"
      data-storage-key="hotel-checkout"
      data-start-register="{{ $shouldStartRegister ? '1' : '0' }}"
      data-prefill='@json($prefillPayload)'>
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <input type="hidden" name="amount" value="{{ $total }}">
      <input type="hidden" name="room_id" value="{{ $room->id }}">
      @foreach($slotSummaries as $i => $s)
        <input type="hidden" name="slots[{{ $i }}][start_date]" value="{{ $s['start_date']->format(HotelHelper::getDateFormat()) }}">
        <input type="hidden" name="slots[{{ $i }}][end_date]" value="{{ $s['end_date']->format(HotelHelper::getDateFormat()) }}">
      @endforeach
      <input type="hidden" name="adults" value="{{ $adults }}">
      <input type="hidden" name="rooms" value="{{ $rooms }}">
      <input type="hidden" name="currency" value="{{ strtoupper(get_application_currency()->title) }}">
      <input type="hidden" name="currency_id" value="{{ get_application_currency_id() }}">
      @if (is_plugin_active('paypal'))
        <input type="hidden" name="callback_url" value="{{ route('payments.paypal.status') }}">
      @endif
      <input type="hidden" name="number_of_guests" value="{{ $adults }}">
      <input type="hidden" name="register_customer" value="{{ old('register_customer', 0) }}" id="register_customer_flag">

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

        @if($services->count() || $foods->count())
          <div class="step-section">
            <h5>Zusatzoptionen</h5>
            <p>Wähle optionale Services oder Verpflegung für deinen Aufenthalt.</p>
            @if($services->count())
              <div class="step-section">
                <h5>Services</h5>
                <div class="addon-grid">
                  @foreach($services as $service)
                    <label class="addon-card custom-checkbox" for="service_{{ $service->id }}">
                      <input type="checkbox" class="service-item" id="service_{{ $service->id }}" name="services[]" value="{{ $service->id }}" @if (in_array($service->id, (array)old('services', $selectedServices))) checked @endif>
                      <div class="addon-meta">
                        <div class="addon-name">{{ $service->name }}</div>
                        <div class="addon-price">{!! $canShowRoomPrices ? format_price($service->price) : $priceInquiryText !!}</div>
                      </div>
                    </label>
                  @endforeach
                </div>
              </div>
            @endif

            @if($foods->count())
              <div class="step-section">
                <h5>Verpflegung</h5>
                <div class="addon-grid">
                  @foreach($foods as $food)
                    <label class="addon-card custom-checkbox" for="food_{{ $food->id }}">
                      <input type="checkbox" class="food-item" id="food_{{ $food->id }}" name="foods[]" value="{{ $food->id }}" @if (in_array($food->id, (array)old('foods', $selectedFoods))) checked @endif>
                      <div class="addon-meta">
                        <div class="addon-name">{{ $food->name }}</div>
                        <div class="addon-price">{!! $canShowRoomPrices ? format_price($food->price) : $priceInquiryText !!}</div>
                      </div>
                    </label>
                  @endforeach
                </div>
              </div>
            @endif
          </div>
        @endif

        <div class="step-section">
          <h5>Ihre Angaben</h5>
          <p>Pflichtfelder sind mit * gekennzeichnet</p>
          <div class="row g-3">
            <div class="col-md-6"><label>Vorname *</label><input id="txt-first_name" name="first_name" class="form-control" value="{{ $contactValues['first_name'] }}" required></div>
            <div class="col-md-6"><label>Nachname *</label><input id="txt-last_name" name="last_name" class="form-control" value="{{ $contactValues['last_name'] }}" required></div>
            <div class="col-md-6"><label>E-Mail *</label><input id="txt-email" name="email" class="form-control" type="email" value="{{ $contactValues['email'] }}" required></div>
            <div class="col-md-6"><label>Telefon *</label><input id="txt-phone" name="phone" class="form-control" value="{{ $contactValues['phone'] }}" required></div>
            <div class="col-md-6"><label>Land</label><input id="txt-country" name="country" class="form-control" value="{{ $contactValues['country'] }}"></div>
            <div class="col-md-6"><label>Bundesland / Provinz</label><input id="txt-state" name="state" class="form-control" value="{{ $contactValues['state'] }}"></div>
            <div class="col-md-6"><label>Stadt</label><input id="txt-city" name="city" class="form-control" value="{{ $contactValues['city'] }}"></div>
            <div class="col-md-6"><label>Adresse</label><input id="txt-address" name="address" class="form-control" value="{{ $contactValues['address'] }}"></div>
            <div class="col-md-6"><label>Postleitzahl</label><input id="txt-zip" name="zip" class="form-control" value="{{ $contactValues['zip'] }}"></div>
            <div class="col-md-6">
              <label>Ankunftszeit</label>
              <select name="arrival_time" id="arrival_time" class="form-select">
                @php($arrivalSelected = old('arrival_time'))
                <option @selected(! $arrivalSelected || $arrivalSelected === __('I do not know'))>{{ __('I do not know') }}</option>
                <option @selected($arrivalSelected === '12:00 - 1:00 ' . __('AM'))>12:00 - 1:00 {{ __('AM') }}</option>
                <option @selected($arrivalSelected === '1:00 - 2:00 ' . __('AM'))>1:00 - 2:00 {{ __('AM') }}</option>
                <option @selected($arrivalSelected === '2:00 - 3:00 ' . __('AM'))>2:00 - 3:00 {{ __('AM') }}</option>
                <option @selected($arrivalSelected === '3:00 - 4:00 ' . __('AM'))>3:00 - 4:00 {{ __('AM') }}</option>
                <option @selected($arrivalSelected === '4:00 - 5:00 ' . __('AM'))>4:00 - 5:00 {{ __('AM') }}</option>
                <option @selected($arrivalSelected === '5:00 - 6:00 ' . __('AM'))>5:00 - 6:00 {{ __('AM') }}</option>
                <option @selected($arrivalSelected === '6:00 - 7:00 ' . __('AM'))>6:00 - 7:00 {{ __('AM') }}</option>
                <option @selected($arrivalSelected === '7:00 - 8:00 ' . __('AM'))>7:00 - 8:00 {{ __('AM') }}</option>
                <option @selected($arrivalSelected === '8:00 - 9:00 ' . __('AM'))>8:00 - 9:00 {{ __('AM') }}</option>
                <option @selected($arrivalSelected === '9:00 - 10:00 ' . __('AM'))>9:00 - 10:00 {{ __('AM') }}</option>
                <option @selected($arrivalSelected === '10:00 - 11:00 ' . __('AM'))>10:00 - 11:00 {{ __('AM') }}</option>
                <option @selected($arrivalSelected === '11:00 - 12:00 ' . __('PM'))>11:00 - 12:00 {{ __('PM') }}</option>
                <option @selected($arrivalSelected === '12:00 - 1:00 ' . __('PM'))>12:00 - 1:00 {{ __('PM') }}</option>
                <option @selected($arrivalSelected === '1:00 - 2:00 ' . __('PM'))>1:00 - 2:00 {{ __('PM') }}</option>
                <option @selected($arrivalSelected === '2:00 - 3:00 ' . __('PM'))>2:00 - 3:00 {{ __('PM') }}</option>
                <option @selected($arrivalSelected === '3:00 - 4:00 ' . __('PM'))>3:00 - 4:00 {{ __('PM') }}</option>
                <option @selected($arrivalSelected === '4:00 - 5:00 ' . __('PM'))>4:00 - 5:00 {{ __('PM') }}</option>
                <option @selected($arrivalSelected === '5:00 - 6:00 ' . __('PM'))>5:00 - 6:00 {{ __('PM') }}</option>
                <option @selected($arrivalSelected === '6:00 - 7:00 ' . __('PM'))>6:00 - 7:00 {{ __('PM') }}</option>
                <option @selected($arrivalSelected === '7:00 - 8:00 ' . __('PM'))>7:00 - 8:00 {{ __('PM') }}</option>
                <option @selected($arrivalSelected === '8:00 - 9:00 ' . __('PM'))>8:00 - 9:00 {{ __('PM') }}</option>
                <option @selected($arrivalSelected === '9:00 - 10:00 ' . __('PM'))>9:00 - 10:00 {{ __('PM') }}</option>
                <option @selected($arrivalSelected === '10:00 - 11:00 ' . __('PM'))>10:00 - 11:00 {{ __('PM') }}</option>
                <option @selected($arrivalSelected === '11:00 - 12:00 ' . __('PM'))>11:00 - 12:00 {{ __('PM') }}</option>
              </select>
            </div>
          </div>
        </div>

        <div class="step-section requests-box">
          <h5>Spezielle Wünsche</h5>
          <textarea id="requests" name="requests" class="form-control" placeholder="{{ __('Write Something') }}...">{{ $requestsValue }}</textarea>
        </div>

        <div class="btnrow">
          <button type="button" class="btnX btn-outline-mint" data-prev>Abbrechen</button>
          <button type="button" class="btnX btn-mint" data-next>Weiter</button>
        </div>
      </div>

      {{-- Step 3 --}}
      <div class="step-panel" data-step="3">
        <div id="formAlertStep3" class="form-alert"></div>

        <div class="booking-facts">
          @if($startLabel24)
            <div><span>Check-in</span>{{ $startLabel24 }}</div>
          @endif
          @if($endLabel24)
            <div><span>Check-out</span>{{ $endLabel24 }}</div>
          @endif
          <div><span>Zimmer</span>{{ $rooms }}</div>
          <div><span>Erwachsene</span>{{ $adults }}</div>
        </div>

        @if (is_plugin_active('payment') && ($defaultPaymentMethod = PaymentMethods::getDefaultMethod()) && get_payment_setting('status', $defaultPaymentMethod))
          <label>Zahlungsmethode</label>
          <ul class="list-group list_payment_method">
            {!! apply_filters(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, null, [
                'amount' => $total,
                'currency' => strtoupper(get_application_currency()->title),
                'name' => $room->name,
                'selected' => PaymentMethods::getSelectedMethod(),
                'default' => $defaultPaymentMethod,
                'selecting' => PaymentMethods::getSelectingMethod(),
            ]) !!}
            {!! PaymentMethods::render() !!}
          </ul>
        @endif

        {!! apply_filters('form_extra_fields_render', null) !!}

        <label class="d-flex align-items-center gap-2 mt-2">
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

    @if ($hotelRules = theme_option('hotel_rules'))
      <div class="cxl-accordion">
        <details>
          <summary>Zentrumregeln</summary>
          <div class="cxl-body">{!! BaseHelper::clean($hotelRules) !!}</div>
        </details>
      </div>
    @endif

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

@endif

