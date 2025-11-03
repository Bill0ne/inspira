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
  Theme::asset()->container('footer')->usePath()->add('checkout-js', 'js/course-checkout.js');
  $startLabel24 = BaseHelper::formatDate($session->start_date, 'd.m.Y H:i');
  $endLabel24   = $session->end_date ? BaseHelper::formatDate($session->end_date, 'd.m.Y H:i') : null;
@endphp

<style>
:root { --mint:#578E88; --gray:#E5E7EB; }

.header,.topbar,.page-title,.breadcrumb,.page-breadcrumb,.hero-banner{display:none!important;}
.checkout-fw .container{max-width:980px;}
body .pt-120{padding-top:24px!important;}
body .pb-40{padding-bottom:24px!important;}
section.checkout-booking-page{background:#fff;}

/* Ticket */
.ticket{display:grid;grid-template-columns:1.1fr 1.4fr 1fr;border-radius:10px;overflow:hidden;background:#fff;box-shadow:0 2px 12px rgba(0,0,0,.05);margin-bottom:28px;}
.ticket__col{padding:20px 22px;display:flex;flex-direction:column;justify-content:center;}
.ticket__media img{width:100%;min-height:220px;object-fit:cover;display:block;}
.ticket__details{background:var(--mint);color:rgba(255,255,255,.95);}
.ticket__details .title,.ticket__totals .title{font-weight:600;font-size:15px;margin-bottom:10px;color:#fff;}
.ticket__details .kv,.ticket__totals .kv{display:flex;justify-content:space-between;margin:4px 0;font-size:13px;line-height:1.6;}
.ticket__totals{background:#000;color:#fff;border-left:2px solid rgba(255,255,255,.08);}
.ticket__totals hr{border:none;height:1px;background:rgba(255,255,255,.15);margin:10px 0;}
.ticket__totals .total{font-size:16px;font-weight:700;}
@media(max-width:768px){
  .ticket{grid-template-columns:1fr;border-radius:12px;}
  .ticket__media img{min-height:180px;}
  .ticket__details,.ticket__totals{padding:18px 20px;}
  .ticket__details{border-bottom:1px solid rgba(255,255,255,.15);}
  .ticket__totals{border-top:1px solid rgba(255,255,255,.15);border-left:none;}
}

/* Stepper */
.stepper-wrap{text-align:center;padding-bottom:24px;margin-bottom:4px;}
.stepper-title{font-size:16px;font-weight:600;color:#3b4a47;margin-bottom:18px;letter-spacing:.3px;}
.stepper{display:flex;align-items:center;justify-content:space-between;width:100%;}
.stepper .line{flex:1;height:2px;background:#E5E7EB;transition:background .3s ease;}
.stepper .line.active{background:var(--mint);}
.stepper .dot{width:13px;height:13px;border-radius:50%;background:#D5D8D7;transition:all .3s ease;box-shadow:0 0 0 3px #fff inset;z-index:2;}
.stepper .dot.active{background:var(--mint);box-shadow:0 0 0 4px rgba(87,142,136,.18);}
@media(max-width:768px){.stepper-title{font-size:15px;margin-bottom:14px;}.stepper .dot{width:11px;height:11px;}}

/* Panels */
.step-panel{display:none;padding:30px 26px 24px;background:#fff;border:none;}
.step-panel.active{display:block;animation:fade .2s ease-out;}
@keyframes fade{from{opacity:.4;transform:translateY(3px);}to{opacity:1;transform:none;}}

/* Inputs */
.form-control{height:46px;border-radius:6px;}
textarea.form-control{min-height:100px;}
.is-invalid{border-color:#dc3545!important;}

/* Coupon & Payment */
.coupon-wrapper{background:#F9F9F9;border:1px solid var(--gray);border-radius:8px;padding:20px;margin-bottom:24px;}
.coupon-wrapper .btn{background:var(--mint)!important;color:#fff!important;border:none!important;}
.list_payment_method{border:1px solid var(--gray);border-radius:8px;margin-bottom:20px;}
.list_payment_method li{padding:14px 16px;border-bottom:1px solid #f0f0f0;}
.list_payment_method li:last-child{border-bottom:none;}

/* Info/Error Box */
.form-alert{display:none;background:#f9f9f9;border:1px solid var(--mint);border-radius:6px;padding:12px 16px;color:#333;font-size:13px;margin-bottom:16px;text-align:center;}

/* Buttons */
.btnrow{display:flex;align-items:center;justify-content:center;gap:14px;margin-top:24px;}
.btnX{display:inline-flex;align-items:center;justify-content:center;height:48px;min-width:150px;font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;border-radius:4px;transition:all .25s ease;}
.btn-outline-mint{border:1px solid var(--mint);color:var(--mint);background:#fff;}
.btn-outline-mint:hover{background:var(--mint);color:#fff;}
.btn-mint{flex:1;max-width:82%;background:var(--mint);color:#fff;border:1px solid var(--mint);}
.btn-mint:hover{filter:brightness(.93);}
@media(max-width:768px){
  .btnrow{flex-direction:column;gap:10px;}      /* Reihenfolge mobile */
  .btnX{width:100%;padding:14px 0;font-size:13px;}
  .btn-mint{order:1;}                            /* Weiter oben */
  .btn-outline-mint{order:2;}                    /* Abbrechen unten */
}

/* Accordion (storno geschlossen default) */
.cxl-accordion details{border-radius:10px;background:#fff;border:1px solid var(--gray);margin-top:20px;}
.cxl-accordion summary{cursor:pointer;padding:14px 18px;font-weight:600;list-style:none;}
.cxl-accordion .cxl-body{padding:0 18px 18px;}
.cxl-accordion summary::after{content:'▾';float:right;transition:transform .3s ease;}
.cxl-accordion details[open] summary::after{transform:rotate(180deg);}
</style>

<section class="checkout-booking-page checkout-fw">
  <div class="container pt-120 pb-40 checkout-booking">

    {{-- Ticket --}}
    <div class="ticket">
      <div class="ticket__col ticket__media">
        <img src="{{ RvMedia::getImageUrl($course->thumbnail, default: RvMedia::getDefaultImage()) }}" alt="{{ $course->name }}">
      </div>
      <div class="ticket__col ticket__details">
        <h5 class="title">Ihre Reservierung</h5>
        <div class="kv"><span>{{ $course->name }}</span></div>
        <div class="kv"><span>Startdatum der Sitzung</span><b>{{ $startLabel24 }}</b></div>
        @if($endLabel24)
          <div class="kv"><span>Enddatum der Sitzung</span><b>{{ $endLabel24 }}</b></div>
        @endif
      </div>
      <div class="ticket__col ticket__totals">
        <h5 class="title">Gesamtpreis</h5>
        <div class="kv"><span>Preis</span><b>{{ format_price($amount) }}</b></div>
        <div class="kv"><span>Rabatt (Coupon)</span><b>{{ format_price($couponAmount) }}</b></div>
        <div class="kv"><span>Steuern</span><b>{{ format_price($taxAmount) }}</b></div>
        <hr>
        <div class="kv total"><span>Gesamt</span><b>{{ format_price($total) }}</b></div>
      </div>
    </div>

    {{-- Stepper --}}
    <div class="stepper-wrap">
      <div class="stepper-title" id="stepTitle">Allgemeine Informationen</div>
      <div class="stepper">
        <div class="dot active" data-step-dot="1"></div>
        <div class="line" data-step-line="1"></div>
        <div class="dot" data-step-dot="2"></div>
        <div class="line" data-step-line="2"></div>
        <div class="dot" data-step-dot="3"></div>
      </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('public.course.booking.checkout') }}" method="POST" id="bookingForm" class="payment-checkout-form">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <input type="hidden" name="amount" value="{{ $total }}">
      <input type="hidden" name="course_id" value="{{ $course->id }}">
      <input type="hidden" name="session_id" value="{{ $session->id }}">
      <input type="hidden" name="currency" value="{{ strtoupper(get_application_currency()->title) }}">
      <input type="hidden" name="currency_id" value="{{ get_application_currency_id() }}">

      {{-- Schritt 1 --}}
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

      {{-- Schritt 2 --}}
      <div class="step-panel" data-step="2">
        <p>Pflichtfelder sind mit * gekennzeichnet</p>
        <div class="form-alert" id="formAlertStep2"></div>

        <div class="row">
          <div class="col-md-6 mb-3"><label>Vorname *</label><input type="text" class="form-control" id="txt-first_name" name="first_name" required value="{{ old('first_name', $customer) }}"></div>
          <div class="col-md-6 mb-3"><label>Nachname *</label><input type="text" class="form-control" id="txt-last_name" name="last_name" required value="{{ old('last_name', $customer) }}"></div>
          <div class="col-md-6 mb-3"><label>E-Mail *</label><input type="email" class="form-control" id="txt-email" name="email" required value="{{ old('email', $customer) }}"></div>
          <div class="col-md-6 mb-3"><label>Telefon *</label><input type="text" class="form-control" id="txt-phone" name="phone" required value="{{ old('phone', $customer) }}"></div>
          <div class="col-md-6 mb-3"><label>Land</label><input type="text" class="form-control" id="txt-country" name="country" value="{{ old('country', $customer) }}"></div>
          <div class="col-md-6 mb-3"><label>Bundesland / Provinz</label><input type="text" class="form-control" id="txt-state" name="state" value="{{ old('state', $customer) }}"></div>
          <div class="col-md-6 mb-3"><label>Stadt</label><input type="text" class="form-control" id="txt-city" name="city" value="{{ old('city', $customer) }}"></div>
          <div class="col-md-6 mb-3"><label>Adresse</label><input type="text" class="form-control" id="txt-address" name="address" value="{{ old('address', $customer) }}"></div>
          <div class="col-md-6 mb-3"><label>Postleitzahl</label><input type="text" class="form-control" id="txt-zip" name="zip" value="{{ old('zip', $customer) }}"></div>
        </div>

        @if(! $customer->id)
          <div class="mb-3"><label><input type="checkbox" id="register-customer" name="register_customer" value="1"> Konto mit den obigen Angaben registrieren?</label></div>
          <div class="row">
            <div class="col-md-6 mb-3"><label>Passwort</label><input type="password" class="form-control" name="password"></div>
            <div class="col-md-6 mb-3"><label>Passwort bestätigen</label><input type="password" class="form-control" name="password_confirmation"></div>
          </div>
        @endif

        <div class="mb-3"><label>Anfragen</label><textarea id="requests" name="requests" class="form-control" placeholder="Schreiben Sie etwas...">{{ old('requests') }}</textarea></div>

        <div class="btnrow">
          <button type="button" class="btnX btn-outline-mint" data-prev>Abbrechen</button>
          <button type="button" class="btnX btn-mint" data-next>Weiter</button>
        </div>
      </div>

      {{-- Schritt 3 --}}
      <div class="step-panel" data-step="3">
        <div class="form-alert" id="formAlertStep3"></div>

        <div class="coupon-wrapper" id="couponBox">@include('plugins/courses::coupons.partials.form')</div>

        <label class="mb-2">Zahlungsmethode</label>
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

        <label class="d-block mb-3">
          <input type="checkbox" id="terms_conditions" name="terms_conditions" value="1">
          Allgemeine Geschäftsbedingungen *
        </label>

        <div class="btnrow">
          <button type="button" class="btnX btn-outline-mint" data-prev>Abbrechen</button>
          <button type="submit" class="btnX btn-mint payment-checkout-btn" data-processing-text="Wird verarbeitet..." data-error-header="Fehler">Abschließen</button>
        </div>
      </div>
    </form>

    {{-- Storno geschlossen (default) --}}
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
