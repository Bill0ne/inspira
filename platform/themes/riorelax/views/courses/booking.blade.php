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

<script>
(function(){
  const form   = document.getElementById('bookingForm');
  const panels = [...document.querySelectorAll('.step-panel')];
  const title  = document.getElementById('stepTitle');
  const dots   = i => document.querySelector('[data-step-dot="'+i+'"]');
  const lines  = i => document.querySelector('[data-step-line="'+i+'"]');
  const titles = ['Allgemeine Informationen','Ihre Angaben','Zahlung & Abschluss'];

  let step = {{ $customer->id ? 2 : 1 }};
  render(step);

  const fieldIds = {
    first: 'txt-first_name',
    last:  'txt-last_name',
    email: 'txt-email',
    phone: 'txt-phone'
  };

  function val(id){ return document.getElementById(id)?.value.trim() || ''; }
  function markInvalid(el, invalid){ if(el) el.classList.toggle('is-invalid', !!invalid); }
  function emailOk(s){ return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(s); }
  function phoneOk(s){ return /^[0-9+\s\-()]+$/.test(s); }

  function showAlert(stepNo, msg){
    const box = document.getElementById(stepNo === 2 ? 'formAlertStep2' : 'formAlertStep3');
    if(!box) return;
    box.textContent = msg;
    box.style.display = msg ? 'block' : 'none';
    if(msg) box.scrollIntoView({behavior:'smooth', block:'center'});
  }

  function clearAlerts(){
    ['formAlertStep2','formAlertStep3'].forEach(id=>{
      const el=document.getElementById(id);
      if(el){ el.style.display='none'; el.textContent=''; }
    });
  }

  function validateStep(stepNo){
    const fFirst = document.getElementById(fieldIds.first);
    const fLast  = document.getElementById(fieldIds.last);
    const fEmail = document.getElementById(fieldIds.email);
    const fPhone = document.getElementById(fieldIds.phone);

    const vFirst = val(fieldIds.first);
    const vLast  = val(fieldIds.last);
    const vEmail = val(fieldIds.email);
    const vPhone = val(fieldIds.phone);

    let errors = [];

    // Pflichtfelder
    if(!vFirst){ errors.push('Vorname ist erforderlich.'); markInvalid(fFirst,true); } else markInvalid(fFirst,false);
    if(!vLast){ errors.push('Nachname ist erforderlich.'); markInvalid(fLast,true); } else markInvalid(fLast,false);

    if(!vEmail){
      errors.push('E-Mail ist erforderlich.'); markInvalid(fEmail,true);
    } else if(!emailOk(vEmail)){
      errors.push('Bitte geben Sie eine gültige E-Mail-Adresse ein.'); markInvalid(fEmail,true);
    } else markInvalid(fEmail,false);

    if(!vPhone){
      errors.push('Telefon ist erforderlich.'); markInvalid(fPhone,true);
    } else if(!phoneOk(vPhone)){
      errors.push('Bitte geben Sie eine gültige Telefonnummer ein.'); markInvalid(fPhone,true);
    } else if(vPhone.replace(/\D/g,'').length < 8){
      errors.push('Die Telefonnummer muss mindestens 8 Ziffern enthalten.'); markInvalid(fPhone,true);
    } else markInvalid(fPhone,false);

    // AGB Pflicht (nur Step 3)
    if(stepNo === 3){
      const terms = document.getElementById('terms_conditions');
      if(!terms || !terms.checked){
        errors.push('Bitte akzeptieren Sie die Allgemeinen Geschäftsbedingungen, um fortzufahren.');
      }
    }

    showAlert(stepNo, errors.length ? ('⚠️ ' + errors[0]) : '');
    return errors.length === 0;
  }

  // Live Validation für Step 2
  form.addEventListener('input', ()=>{
    if(step===2){
      const nextBtn = form.querySelector('[data-step="2"] [data-next]');
      if(nextBtn){
        const ok = validateStep(2);
        nextBtn.disabled = !ok;
        if(ok) showAlert(2,'');
      }
    }
  });

  // Navigation Next / Prev + Final Submit
  form.addEventListener('click', e=>{
    const next   = e.target.closest('[data-next]');
    const prev   = e.target.closest('[data-prev]');
    const finish = e.target.closest('.payment-checkout-btn'); // Abschließen

    if(next){
      e.preventDefault();
      clearAlerts();
      if(step===1){ step=2; render(step); return; }
      if(step===2 && !validateStep(2)) return;
      step = Math.min(step+1,3);
      render(step);
    }

    if(prev){
      e.preventDefault();
      clearAlerts();
      step = Math.max(step-1,1);
      render(step);
    }

    // Abschließen manuell prüfen
    if(finish){
      e.preventDefault();
      clearAlerts();
      const ok2 = validateStep(2);
      const ok3 = validateStep(3);
      if(!(ok2 && ok3)){
        if(!ok2 && step!==2){ step=2; render(step); }
        else if(!ok3 && step!==3){ step=3; render(step); }
        return;
      }
      form.submit();
    }
  });

  // AGB Checkbox aktiviert/deaktiviert live den Abschließen-Button
  const termsBox = document.getElementById('terms_conditions');
  if(termsBox){
    const submitBtn = form.querySelector('.payment-checkout-btn');
    const toggleState = () => submitBtn.disabled = !termsBox.checked;
    toggleState();
    termsBox.addEventListener('change', toggleState);
  }

  // Step-Render
  function render(s){
    panels.forEach(p=>p.classList.toggle('active', p.dataset.step==s));
    title.textContent = titles[s-1];
    [1,2,3].forEach(i=>{
      const d=dots(i), l=lines(i);
      if(d)d.classList.toggle('active',i<=s);
      if(l)l.classList.toggle('active',i<s);
    });
    // Coupon immer offen
    const c=document.querySelector('#couponBox .collapse');
    if(c&&!c.classList.contains('show'))c.classList.add('show');
  }
})();
</script>

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
