@if (is_plugin_active('payment'))
    <link rel="stylesheet" href="{{ asset('vendor/core/plugins/payment/css/payment.css') }}?v=1.0.3">
    @php
        Theme::asset()->container('header')->usePath()->add('jquery', 'plugins/jquery.min.js');
        Theme::asset()->container('header')->add('payment-js', 'vendor/core/plugins/payment/js/payment.js');
    @endphp
    {!! apply_filters(PAYMENT_FILTER_HEADER_ASSETS, null) !!}
@endif

@php
    // Seitentitel unterdrücken – wir zeigen nur den Ticket-Header
    Theme::set('pageTitle', '');

    Theme::asset()->container('footer')->usePath()->add('checkout-js', 'js/course-checkout.js');

    // Labels im 24h-Format
    $startLabel24 = BaseHelper::formatDate($session->start_date, 'd.m.Y H:i');
    $endLabel24   = $session->end_date ? BaseHelper::formatDate($session->end_date, 'd.m.Y H:i') : null;
@endphp

<style>
/* ======= Layout-Reset: Header/Breadcrumb ausblenden, Page kompakt ======= */
.header, .topbar, .page-title, .breadcrumb, .page-breadcrumb, .hero-banner { display:none !important; }
.checkout-fw .container { max-width: 980px; }
body .pt-120 { padding-top: 24px !important; }
body .pb-40  { padding-bottom: 24px !important; }

/* ======= Karten/Container ======= */
.checkout-fw .card{ background:#fff; border-radius:10px; box-shadow:0 1px 6px rgba(0,0,0,.06); }
.checkout-fw .card-body{ padding:16px; }
.checkout-fw .form-card{ margin-bottom:16px; }
.checkout-fw .form-card .card-body{ padding:20px; }
.checkout-fw .payment-checkout-btn{ width:100%; }

/* ======= Ticket (Header) ======= */
.checkout-fw :root{ --mint:#578E88; --ink:#111827; }
.ticket{
  display:grid; grid-template-columns: 1.15fr 1.35fr 1fr;
  border-radius:8px; overflow:hidden;
  box-shadow:0 4px 18px rgba(0,0,0,.06); background:#fff; margin-bottom:16px;
}
@media (max-width: 767.98px){ .ticket{ grid-template-columns:1fr; } }
.ticket__col{ padding:16px 18px; }
.ticket__media{ padding:0; position:relative; background:#fff; }
.ticket__media img{ width:100%; height:100%; min-height:220px; object-fit:cover; display:block; border-radius:0; }
.ticket__details{ position:relative; background:var(--mint); color:#F3F3F3; }
.ticket__details::before{ content:""; position:absolute; left:0; top:0; bottom:0; border-left:2px dashed rgba(255,255,255,.95); }
.ticket__totals{ background:#000; color:#fff; }
.ticket .title{ margin:0 0 8px; font-weight:600; font-size:16px; }
.ticket .kv{ display:flex; justify-content:space-between; gap:12px; margin:6px 0; font-size:13px; line-height:18px; }
.ticket .kv b{ font-weight:600; }
.ticket .total{ margin-top:8px; font-size:18px; font-weight:700; }
.ticket__totals hr{ border:0; height:1px; background:rgba(255,255,255,.12); margin:10px 0; }
/* Farbschutz */
.checkout-fw .ticket .ticket__details,
.checkout-fw .ticket .ticket__details *{ background:var(--mint)!important; color:#F3F3F3!important; }

/* ======= Stepper ======= */
.stepper{ display:flex; align-items:center; justify-content:space-between; gap:12px; margin:12px 0 18px; }
.stepper .dot{ width:10px; height:10px; border-radius:50%; background:#D1D5DB; position:relative; }
.stepper .dot.active{ background:#578E88; }
.stepper .line{ flex:1; height:2px; background:#E5E7EB; }
.stepper .line.active{ background:#578E88; }

/* ======= Step-Panels (ohne Scroll) ======= */
.steps-wrap{ position:relative; min-height: 420px; display:grid; }
.step-panel{ display:none; }
.step-panel.active{ display:block; animation: fade .18s ease-out; }
@keyframes fade{ from{opacity:.3; transform:translateY(4px);} to{opacity:1; transform:none;} }

/* Felder kompakt */
.checkout-fw .form-control{ height:48px; }

/* Aktionbuttons */
.btn-row{ display:flex; gap:12px; }
.btn-secondary{ background:#EDEDED; color:#111; border:none; }
.btn-primary{ background:#578E88; border:none; }
.btn-primary:hover{ opacity:.95; }

/* ======= Storno-Accordion ======= */
.cxl-accordion details{ border-radius:10px; background:#fff; box-shadow:0 1px 6px rgba(0,0,0,.06); }
.cxl-accordion summary{ cursor:pointer; padding:16px 18px; font-weight:600; list-style:none; }
.cxl-accordion .cxl-body{ padding:0 18px 18px; }
</style>

<section class="checkout-booking-page checkout-fw">
  <div class="container pt-120 pb-40 checkout-booking">

    {{-- =================== HEADER: Ticket =================== --}}
    <div class="ticket">
      <div class="ticket__col ticket__media">
        <img src="{{ RvMedia::getImageUrl($course->thumbnail, default: RvMedia::getDefaultImage()) }}"
             alt="{{ $course->name }}">
      </div>

      <div class="ticket__col ticket__details">
        <h5 class="title">{{ __('Ihre Reservierung') }}</h5>
        <div class="kv"><span>{{ $course->name }}</span></div>
        <div class="kv">
          <span>{{ __('Startdatum der Sitzung') }}</span>
          <b class="session-start-date">{{ $startLabel24 }}</b>
        </div>
        @if($endLabel24)
          <div class="kv">
            <span>{{ __('Enddatum der Sitzung') }}</span>
            <b class="session-end-date">{{ $endLabel24 }}</b>
          </div>
        @endif
      </div>

      <div class="ticket__col ticket__totals">
        <h5 class="title">{{ __('Gesamtpreis') }}</h5>
        @php $priceDifference = $basePrice - $amount; @endphp

        @if($amount < $basePrice)
          <div class="kv">
            <span>{{ __('Originalpreis') }}</span>
            <b class="text-muted text-decoration-line-through">{{ format_price($basePrice) }}</b>
          </div>
          <div class="kv">
            <span>{{ __('Rabattierter Preis') }}</span>
            <b class="text-success fw-bold amount-text">{{ format_price($amount) }}</b>
          </div>
          <div class="kv small text-success mt-1">
            <i class="fas fa-tag me-1"></i>
            {{ __('You save :amount', ['amount' => format_price(abs($priceDifference))]) }}
          </div>
        @else
          <div class="kv">
            <span>{{ __('Preis') }}</span>
            <b class="amount-text">{{ format_price($amount) }}</b>
          </div>
        @endif

        <div class="kv">
          <span>{{ __('Rabatt (Coupon)') }}</span>
          <b class="discount-text">{{ format_price($couponAmount) }}</b>
        </div>
        <div class="kv">
          <span>{{ __('Steuern') }}</span>
          <b class="tax-text">{{ format_price($taxAmount) }}</b>
        </div>

        <hr>

        <div class="kv total">
          <span>{{ __('Gesamt') }}</span>
          <span class="total-amount-text fw-bold">{{ format_price($total) }}</span>
        </div>
      </div>
    </div>

    {{-- =================== MITTE: 3-Schritt-Form =================== --}}
    <div class="card form-card shadow-block">
      <div class="card-body">

        {{-- Stepper --}}
        <div class="stepper" id="stepper">
          <div class="dot active" data-step-dot="1"></div>
          <div class="line" data-step-line="1"></div>
          <div class="dot" data-step-dot="2"></div>
          <div class="line" data-step-line="2"></div>
          <div class="dot" data-step-dot="3"></div>
        </div>

        <form action="{{ route('public.course.booking.checkout') }}" class="booking-form-main payment-checkout-form" method="POST" id="bookingForm">
          @csrf
          <input type="hidden" name="token" value="{{ $token }}">
          <input type="hidden" name="amount" value="{{ $total }}">
          <input type="hidden" name="course_id" value="{{ $course->id }}">
          <input type="hidden" name="session_id" value="{{ $session->id }}">
          <input type="hidden" name="currency" value="{{ strtoupper(get_application_currency()->title) }}">
          <input type="hidden" name="currency_id" value="{{ get_application_currency_id() }}">
          @if (is_plugin_active('paypal'))
            <input type="hidden" name="callback_url" value="{{ route('payments.paypal.status') }}">
          @endif

          <div class="steps-wrap">

            {{-- ===== Schritt 1: Anmeldung ===== --}}
            <div class="step-panel active" data-step="1">
              @if ($customer->id)
                <p class="mb-3">{{ __('Angemeldet als') }} <b>{{ $customer->name ?? $customer->email }}</b></p>
                <div class="btn-row">
                  <button type="button" class="btn btn-primary" data-next>{{ __('Weiter') }}</button>
                  <a class="btn btn-secondary" href="{{ url()->previous() }}">{{ __('Abbrechen') }}</a>
                </div>
              @else
                <div class="mb-3">
                  <p class="mb-2">{{ __('Wie möchtest du fortfahren?') }}</p>
                  <div class="btn-row">
                    <button type="button" class="btn btn-primary" data-next>{{ __('Als Gast buchen') }}</button>
                    <a href="{{ route('customer.login') }}?redirect={{ urlencode(request()->fullUrl()) }}" class="btn btn-secondary">
                      {{ __('Einloggen') }}
                    </a>
                  </div>
                </div>
              @endif
            </div>

            {{-- ===== Schritt 2: Persönliche Angaben ===== --}}
            <div class="step-panel" data-step="2">
              <p class="mb-20">{{ __('Required fields are followed by *') }}</p>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group mb-20">
                    <label for="txt-first-name">{{ __('First Name') }} <span class="required">*</span></label>
                    <input type="text" name="first_name" id="txt-first-name" class="form-control" required value="{{ old('first_name', $customer) }}">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group mb-20">
                    <label for="txt-last-name">{{ __('Last Name') }} <span class="required">*</span></label>
                    <input type="text" name="last_name" id="txt-last-name" class="form-control" required value="{{ old('last_name', $customer) }}">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group mb-20">
                    <label for="txt-email">{{ __('Email') }} <span class="required">*</span></label>
                    <input type="email" name="email" id="txt-email" class="form-control" required value="{{ old('email', $customer) }}">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group mb-20">
                    <label for="txt-phone">{{ __('Phone') }} <span class="required">*</span></label>
                    <input type="text" name="phone" id="txt-phone" class="form-control" required value="{{ old('phone', $customer) }}">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group mb-20">
                    <label for="txt-country">{{ __('Country') }}</label>
                    <input type="text" name="country" id="txt-country" class="form-control" value="{{ old('country', $customer) }}">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group mb-20">
                    <label for="txt-state">{{ __('State / Province') }}</label>
                    <input type="text" name="state" id="txt-state" class="form-control" value="{{ old('state', $customer) }}">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group mb-20">
                    <label for="txt-city">{{ __('City') }}</label>
                    <input type="text" name="city" id="txt-city" class="form-control" value="{{ old('city', $customer) }}">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group mb-20">
                    <label for="txt-address">{{ __('Address') }}</label>
                    <input type="text" name="address" id="txt-address" class="form-control" value="{{ old('address', $customer) }}">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group mb-20">
                    <label for="txt-zip">{{ __('Postal / Zip code') }}</label>
                    <input type="text" name="zip" id="txt-zip" class="form-control" value="{{ old('zip', $customer) }}">
                  </div>
                </div>
              </div>

              @if(! $customer->id)
                <div class="create-customer">
                  <div class="row">
                    <div class="form-group mb-20 custom-checkbox d-block">
                      <label for="register-customer" class="w-100">
                        <input type="checkbox" id="register-customer" name="register_customer" value="1">
                        {{ __('Register an account with above information?') }}
                        <span></span>
                      </label>
                    </div>
                  </div>
                  <div class="row form-create-customer-password">
                    <div class="col-md-6">
                      <div class="form-group mb-20">
                        <label for="password">{{ __('Password') }}</label>
                        <input type="password" name="password" id="password" class="form-control">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group mb-20">
                        <label for="password_confirmation">{{ __('Password confirm') }}</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                      </div>
                    </div>
                  </div>
                </div>
              @endif

              <div class="form-group mb-20">
                <label for="requests">{{ __('Requests') }}</label>
                <textarea name="requests" rows="3" class="form-control" id="requests" placeholder="{{ __('Write Something') }}...">{{ old('requests') }}</textarea>
              </div>

              <div class="btn-row">
                <button type="button" class="btn btn-secondary" data-prev>{{ __('Abbrechen') }}</button>
                <button type="button" class="btn btn-primary" data-next>{{ __('Weiter') }}</button>
              </div>
            </div>

            {{-- ===== Schritt 3: Zahlung & Abschluss ===== --}}
            <div class="step-panel" data-step="3">
              @include('plugins/courses::coupons.partials.form')

              @if (is_plugin_active('payment') && ($defaultPaymentMethod = PaymentMethods::getDefaultMethod()) && get_payment_setting('status', $defaultPaymentMethod))
                <div class="form-group mb-20">
                  <label>{{ __('Payment method') }}</label>
                  <ul class="list-group list_payment_method">
                    {!! apply_filters(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, null, [
                        'amount' => $total,
                        'currency' => strtoupper(get_application_currency()->title),
                        'name' => $course->name,
                        'selected' => PaymentMethods::getSelectedMethod(),
                        'default' => $defaultPaymentMethod,
                        'selecting' => PaymentMethods::getSelectingMethod(),
                    ]) !!}
                    {!! PaymentMethods::render() !!}
                  </ul>
                </div>
              @endif

              {!! apply_filters('form_extra_fields_render', null) !!}

              <div class="form-group mb-20 custom-checkbox d-block">
                <label for="terms_conditions" class="w-100">
                  <input type="checkbox" id="terms_conditions" name="terms_conditions" value="1" @if (old('terms_conditions') == 1) checked @endif>
                  {{ __('Terms & conditions *') }} <span></span>
                </label>
              </div>

              <div class="btn-row">
                <button type="button" class="btn btn-secondary" data-prev>{{ __('Abbrechen') }}</button>
                <button type="submit" class="btn btn-primary payment-checkout-btn"
                        data-processing-text="{{ __('Processing. Please wait...') }}"
                        data-error-header="{{ __('Error') }}">{{ __('Abschließen') }}</button>
              </div>
            </div>

          </div> {{-- /steps-wrap --}}
        </form>
      </div>
    </div>

    {{-- =================== UNTERER BEREICH: Storno als Accordion =================== --}}
    @if ($cancellation = theme_option('cancellation'))
      <div class="cxl-accordion">
        <details>
          <summary>{{ __('Stornierungsbedingungen') }}</summary>
          <div class="cxl-body">
            {!! BaseHelper::clean($cancellation) !!}
          </div>
        </details>
      </div>
    @endif

    {{-- ===== Optional: Standard-Footer bleibt erhalten (Theme) ===== --}}

  </div>
</section>

@if (is_plugin_active('payment'))
  {!! apply_filters(PAYMENT_FILTER_FOOTER_ASSETS, null) !!}
  @php
      Theme::asset()->container('footer')
          ->add('js-validation', 'vendor/core/core/js-validation/js/js-validation.js', ['jquery'])
          ->writeContent('checkout-validator', JsValidator::formRequest(Botble\Hotel\Http\Requests\CheckoutRequest::class))
  @endphp
@endif

{{-- ===== Minimal-JS für Step-Navigation (Vanilla, keine externe Lib) ===== --}}
<script>
(function(){
  const form   = document.getElementById('bookingForm');
  const panels = Array.from(document.querySelectorAll('.step-panel'));
  const dots   = stepIdx => document.querySelector('[data-step-dot="'+stepIdx+'"]');
  const lines  = lineIdx => document.querySelector('[data-step-line="'+lineIdx+'"]');

  let step = {{ $customer->id ? 2 : 1 }}; // eingeloggte User starten direkt bei Schritt 2
  show(step);

  // Next/Prev Handler
  form.addEventListener('click', function(e){
    const next = e.target.closest('[data-next]');
    const prev = e.target.closest('[data-prev]');
    if(next){ e.preventDefault(); go(step+1); }
    if(prev){ e.preventDefault(); go(step-1); }
  });

  function go(target){
    step = Math.min(Math.max(target,1),3);
    show(step);
    // Scroll-Fix: oben bleiben (One-Page ohne Scroll)
    window.scrollTo({ top: document.querySelector('.checkout-booking').offsetTop, behavior: 'instant' });
  }

  function show(s){
    panels.forEach(p => p.classList.toggle('active', p.dataset.step == s));
    // Stepper-Status
    [1,2,3].forEach(i=>{
      const d = dots(i); if(!d) return;
      d.classList.toggle('active', i<=s);
      const l = lines(i); if(l) l.classList.toggle('active', i < s);
    });
  }
})();
</script>
