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
    Theme::asset()->container('footer')->usePath()->add('checkout-js', 'js/checkout.js');

    $startLabel24 = $displayStart ? BaseHelper::formatDate($displayStart, 'd.m.Y H:i') : null;
    $endLabel24 = $displayEnd ? BaseHelper::formatDate($displayEnd, 'd.m.Y H:i') : null;
    $isLoggedIn = auth('customer')->check() || auth()->check();

    $roomPriceDisplay = $totalRoomPrice ?? 0;
    $extrasAmountDisplay = $extrasAmount ?? 0;
@endphp

<style>
:root{--mint:#578E88;--gray:#E5E7EB;}
.header,.topbar,.page-title,.breadcrumb,.page-breadcrumb,.hero-banner{display:none!important;}
.checkout-fw .container{max-width:980px;}
body .pt-120{padding-top:24px!important;}
body .pb-40{padding-bottom:24px!important;}
section.checkout-booking-page{background:#fff;}

/* ==== Ticket Header ==== */
.ticket{display:grid;grid-template-columns:1.1fr 1.4fr 1fr;border-radius:10px;overflow:hidden;background:#fff;box-shadow:0 2px 12px rgba(0,0,0,0.05);margin-bottom:28px;}
.ticket__col{padding:20px 22px;display:flex;flex-direction:column;justify-content:center;gap:8px;}
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
.step-section{margin-bottom:22px;}
.step-section h5{font-size:15px;font-weight:600;margin-bottom:12px;color:#17463f;}
.step-section p{margin-bottom:14px;}

/* ==== Alert Box ==== */
.form-alert{display:none;margin:0 0 16px;padding:12px 16px;border-radius:6px;background:#fff3f3;color:#b71c1c;font-size:14px;border:1px solid #f1b4b4;}

/* ==== Coupon ==== */
.coupon-wrapper{background:#F9F9F9;border:1px solid var(--gray);border-radius:8px;padding:20px;margin-bottom:24px;}
.coupon-wrapper label{font-weight:500;}
.coupon-wrapper .coupon-box{display:flex;flex-direction:column;gap:14px;}
.coupon-wrapper .coupon-form{margin:0;}
.coupon-wrapper .coupon-feedback{border-radius:8px;padding:16px 18px;}
.coupon-wrapper .coupon-feedback .btn{color:#17463f;font-weight:600;}
.coupon-wrapper .coupon-input-group{display:flex;align-items:stretch;gap:12px;}
.coupon-wrapper .coupon-input-group>.form-control{flex:1 1 auto;min-width:200px;border-radius:6px;}
.coupon-wrapper .coupon-input-group>.btn{flex:0 0 auto;padding:12px 22px;font-weight:600;border-radius:6px;}
.coupon-wrapper .apply-coupon-code{background:var(--mint)!important;color:#fff!important;border:none!important;}
.coupon-wrapper .btn-remove-coupon{display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:999px;border:1px solid rgba(87,142,136,0.35);background:#fff;color:#b23a48;font-weight:600;transition:all .2s ease;box-shadow:0 2px 6px rgba(0,0,0,0.05);}
.coupon-wrapper .btn-remove-coupon:hover{background:#fff5f5;color:#922b21;border-color:rgba(178,58,72,0.45);box-shadow:0 4px 12px rgba(0,0,0,0.08);}
.coupon-wrapper .btn-remove-coupon svg{width:16px;height:16px;}
@media(max-width:768px){
  .coupon-wrapper{padding:18px;}
}
@media(max-width:575px){
  .coupon-wrapper .coupon-input-group{flex-direction:column;gap:10px;}
  .coupon-wrapper .coupon-input-group>.form-control{min-width:0;width:100%;}
  .coupon-wrapper .coupon-input-group>.btn{width:100%;padding:12px;font-size:14px;}
  .coupon-wrapper .toggle-coupon-form{font-size:14px;}
}

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

<section class="checkout-booking-page checkout-fw">
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
        <div class="kv"><span>Preis</span><b class="amount-text">{{ format_price($roomPriceDisplay) }}</b></div>
        @if($extrasAmountDisplay > 0)
          <div class="kv"><span>Zusatzleistungen</span><b>{{ format_price($extrasAmountDisplay) }}</b></div>
        @endif
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
    <form action="{{ route('public.booking.checkout') }}" method="POST" id="bookingForm" class="payment-checkout-form">
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
                        <div class="addon-price">{{ $isLoggedIn ? format_price($service->price) : __('Preis nach Login') }}</div>
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
                        <div class="addon-price">{{ $isLoggedIn ? format_price($food->price) : __('Preis nach Login') }}</div>
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
            <div class="col-md-6"><label>Vorname *</label><input id="txt-first_name" name="first_name" class="form-control" value="{{ old('first_name', optional($customer)->first_name) }}" required></div>
            <div class="col-md-6"><label>Nachname *</label><input id="txt-last_name" name="last_name" class="form-control" value="{{ old('last_name', optional($customer)->last_name) }}" required></div>
            <div class="col-md-6"><label>E-Mail *</label><input id="txt-email" name="email" class="form-control" type="email" value="{{ old('email', optional($customer)->email) }}" required></div>
            <div class="col-md-6"><label>Telefon *</label><input id="txt-phone" name="phone" class="form-control" value="{{ old('phone', optional($customer)->phone) }}" required></div>
            <div class="col-md-6"><label>Land</label><input id="txt-country" name="country" class="form-control" value="{{ old('country', optional($customer)->country) }}"></div>
            <div class="col-md-6"><label>Bundesland / Provinz</label><input id="txt-state" name="state" class="form-control" value="{{ old('state', optional($customer)->state) }}"></div>
            <div class="col-md-6"><label>Stadt</label><input id="txt-city" name="city" class="form-control" value="{{ old('city', optional($customer)->city) }}"></div>
            <div class="col-md-6"><label>Adresse</label><input id="txt-address" name="address" class="form-control" value="{{ old('address', optional($customer)->address) }}"></div>
            <div class="col-md-6"><label>Postleitzahl</label><input id="txt-zip" name="zip" class="form-control" value="{{ old('zip', optional($customer)->zip) }}"></div>
            <div class="col-md-6">
              <label>Ankunftszeit</label>
              <select name="arrival_time" id="arrival_time" class="form-select">
                <option>{{ __('I do not know') }}</option>
                <option>12:00 - 1:00 {{ __('AM') }}</option>
                <option>1:00 - 2:00 {{ __('AM') }}</option>
                <option>2:00 - 3:00 {{ __('AM') }}</option>
                <option>3:00 - 4:00 {{ __('AM') }}</option>
                <option>4:00 - 5:00 {{ __('AM') }}</option>
                <option>5:00 - 6:00 {{ __('AM') }}</option>
                <option>6:00 - 7:00 {{ __('AM') }}</option>
                <option>7:00 - 8:00 {{ __('AM') }}</option>
                <option>8:00 - 9:00 {{ __('AM') }}</option>
                <option>9:00 - 10:00 {{ __('AM') }}</option>
                <option>10:00 - 11:00 {{ __('AM') }}</option>
                <option>11:00 - 12:00 {{ __('PM') }}</option>
                <option>12:00 - 1:00 {{ __('PM') }}</option>
                <option>1:00 - 2:00 {{ __('PM') }}</option>
                <option>2:00 - 3:00 {{ __('PM') }}</option>
                <option>3:00 - 4:00 {{ __('PM') }}</option>
                <option>4:00 - 5:00 {{ __('PM') }}</option>
                <option>5:00 - 6:00 {{ __('PM') }}</option>
                <option>6:00 - 7:00 {{ __('PM') }}</option>
                <option>7:00 - 8:00 {{ __('PM') }}</option>
                <option>8:00 - 9:00 {{ __('PM') }}</option>
                <option>9:00 - 10:00 {{ __('PM') }}</option>
                <option>10:00 - 11:00 {{ __('PM') }}</option>
                <option>11:00 - 12:00 {{ __('PM') }}</option>
              </select>
            </div>
          </div>
        </div>

        @if(! $customer->id)
          <div class="step-section create-customer">
            <label class="addon-card custom-checkbox" for="register-customer">
              <input type="checkbox" id="register-customer" name="register_customer" value="1">
              <div class="addon-meta">
                <div class="addon-name">{{ __('Register an account with above information?') }}</div>
                <div class="addon-price text-muted">{{ __('Passwort wird im nächsten Schritt festgelegt.') }}</div>
              </div>
            </label>
            <div class="row g-3 mt-3 d-none form-create-customer-password">
              <div class="col-md-6"><label>{{ __('Password') }}</label><input type="password" name="password" class="form-control"></div>
              <div class="col-md-6"><label>{{ __('Password confirm') }}</label><input type="password" name="password_confirmation" class="form-control"></div>
            </div>
          </div>
        @endif

        <div class="step-section requests-box">
          <h5>Spezielle Wünsche</h5>
          <textarea id="requests" name="requests" class="form-control" placeholder="{{ __('Write Something') }}...">{{ old('requests') }}</textarea>
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

        <div class="coupon-wrapper" id="couponBox">@include('plugins/hotel::coupons.partials.form')</div>

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

        <label class="d-flex align-items-center gap-2 mt-2"><input type="checkbox" id="terms_conditions" name="terms_conditions" value="1"> <span>Allgemeine Geschäftsbedingungen *</span></label>

        <div class="btnrow">
          <button type="button" class="btnX btn-outline-mint" data-prev>Abbrechen</button>
          <button type="submit" class="btnX btn-mint payment-checkout-btn" data-processing-text="Wird verarbeitet..." data-error-header="Fehler">Abschließen</button>
        </div>
      </div>
    </form>

    @if ($hotelRules = theme_option('hotel_rules'))
      <div class="cxl-accordion">
        <details>
          <summary>Hotelregeln</summary>
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

@if (is_plugin_active('payment'))
  {!! apply_filters(PAYMENT_FILTER_FOOTER_ASSETS, null) !!}
@endif

@php
    Theme::asset()->container('footer')
        ->add('js-validation', 'vendor/core/core/js-validation/js/js-validation.js', ['jquery'])
        ->writeContent('checkout-validator', JsValidator::formRequest(Botble\Hotel\Http\Requests\CheckoutRequest::class));
@endphp

{{-- ==== JS ==== --}}
<script>
(function () {
  const form = document.getElementById('bookingForm');
  if (!form) return;

  // === STEP & UI ===
  const panels = [...document.querySelectorAll('.step-panel')];
  const title  = document.getElementById('stepTitle');
  const dots   = i => document.querySelector('[data-step-dot="'+ i +'"]');
  const lines  = i => document.querySelector('[data-step-line="'+ i +'"]');
  const titles = ['Allgemeine Informationen','Ihre Angaben','Zahlung & Abschluss'];
  let step     = {{ $customer->id ? 2 : 1 }};
  render(step);

  // === FIELDS ===
  const fieldIds = { first:'txt-first_name', last:'txt-last_name', email:'txt-email', phone:'txt-phone' };
  const val      = id => (document.getElementById(id)?.value.trim() || '');
  const emailOk  = s => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(s);
  const phoneOk  = s => /^[0-9+\s\-()]+$/.test(s);
  const markInvalid = (el,b) => el && el.classList.toggle('is-invalid',!!b);
  let step2ValidationActive = false;
  let step3Attempted        = false;

  // === ALERTS ===
  function showAlert(stepNo,msg){
    const id  = stepNo===2?'formAlertStep2':'formAlertStep3';
    const box = document.getElementById(id);
    if(!box)return;
    box.textContent = msg || '';
    box.style.display = msg ? 'block' : 'none';
    if(msg) box.scrollIntoView({behavior:'smooth',block:'center'});
  }
  function clearAlerts(){
    ['formAlertStep2','formAlertStep3'].forEach(id=>{
      const el=document.getElementById(id);
      if(el){el.style.display='none';el.textContent='';}
    });
  }

  // === VALIDATION ===
  function validateStep(stepNo,{activate=false}={}) {
    const fF=document.getElementById(fieldIds.first),
          fL=document.getElementById(fieldIds.last),
          fE=document.getElementById(fieldIds.email),
          fP=document.getElementById(fieldIds.phone);
    const vF=val(fieldIds.first),
          vL=val(fieldIds.last),
          vE=val(fieldIds.email),
          vP=val(fieldIds.phone);
    const errors=[];
    if(stepNo===2&&activate) step2ValidationActive=true;
    const mark = stepNo!==2||step2ValidationActive;

    if(!vF){errors.push('Vorname ist erforderlich.');if(mark)markInvalid(fF,true);}else if(mark)markInvalid(fF,false);
    if(!vL){errors.push('Nachname ist erforderlich.');if(mark)markInvalid(fL,true);}else if(mark)markInvalid(fL,false);
    if(!vE){errors.push('E-Mail ist erforderlich.');if(mark)markInvalid(fE,true);}
    else if(!emailOk(vE)){errors.push('Bitte gültige E-Mail-Adresse angeben.');if(mark)markInvalid(fE,true);}else if(mark)markInvalid(fE,false);
    if(!vP){errors.push('Telefon ist erforderlich.');if(mark)markInvalid(fP,true);}
    else if(!phoneOk(vP)){errors.push('Bitte gültige Telefonnummer angeben.');if(mark)markInvalid(fP,true);}
    else if(vP.replace(/\D/g,'').length<8){errors.push('Telefonnummer muss min. 8 Ziffern enthalten.');if(mark)markInvalid(fP,true);}else if(mark)markInvalid(fP,false);

    if(stepNo===3){
      const terms=document.getElementById('terms_conditions');
      if(!terms||!terms.checked)errors.push('Bitte akzeptieren Sie die Allgemeinen Geschäftsbedingungen.');
    }

    showAlert(stepNo,errors.length?('⚠️ '+errors[0]):'');
    return errors.length===0;
  }

  // === LOGIC ===
  function attemptFinalization(){
    const ok2 = validateStep(2,{activate:true});
    const ok3 = validateStep(3,{activate:true});
    return ok2 && ok3;
  }

  // === NAVIGATION ===
  const nextButtons = form.querySelectorAll('[data-next]');
  const prevButtons = form.querySelectorAll('[data-prev]');

  nextButtons.forEach(btn=>{
    btn.type='button';
    btn.addEventListener('click',e=>{
      e.preventDefault();
      clearAlerts();
      if(step===1){
        step=2;
        render(step);
        return;
      }
      if(step===2){
        const ok2=validateStep(2,{activate:true});
        if(!ok2)return;
        step=3;
        render(step);
        return;
      }
    });
  });

  prevButtons.forEach(btn=>{
    btn.type='button';
    btn.addEventListener('click',e=>{
      e.preventDefault();
      clearAlerts();
      step=Math.max(1,step-1);
      render(step);
    });
  });

  // === SUBMIT ===
  form.addEventListener('submit',e=>{
    e.preventDefault();
    if(attemptFinalization()) form.submit();
  });

  // === AUTO-VALIDATE ON INPUT ===
  form.addEventListener('input',()=>{
    if(step===2&&step2ValidationActive) validateStep(2);
  });

  // === TERMS TOGGLE ===
  const termsBox=document.getElementById('terms_conditions');
  if(termsBox){
    const submitBtn=form.querySelector('.payment-checkout-btn');
    const toggleState=()=>{
      if(submitBtn) submitBtn.disabled=!termsBox.checked;
      if(termsBox.checked&&step3Attempted) showAlert(3,'');
    };
    toggleState();
    termsBox.addEventListener('change',()=>{
      toggleState();
      if(step3Attempted) validateStep(3,{activate:true});
    });
  }

  // === RENDER ===
  function render(s){
    panels.forEach(p=>p.classList.toggle('active',p.dataset.step==s));
    if(title) title.textContent=titles[s-1];
    [1,2,3].forEach(i=>{
      const d=dots(i),l=lines(i);
      if(d)d.classList.toggle('active',i<=s);
      if(l)l.classList.toggle('active',i<s);
    });
    const c=document.querySelector('#couponBox .collapse');
    if(c&&!c.classList.contains('show')) c.classList.add('show');
  }
})();
</script>
