@if (is_plugin_active('payment'))
    <link rel="stylesheet" href="{{ asset('vendor/core/plugins/payment/css/payment.css') }}?v=1.0.3">
    @php
        Theme::asset()->container('header')->usePath()->add('jquery', 'plugins/jquery.min.js');
        Theme::asset()->container('header')->add('payment-js', 'vendor/core/plugins/payment/js/payment.js');
    @endphp
    {!! apply_filters(PAYMENT_FILTER_HEADER_ASSETS, null) !!}
@endif

@php
    Theme::set('pageTitle', __('Booking'));
    Theme::asset()->container('footer')->usePath()->add('checkout-js', 'js/course-checkout.js');

    // Labels im 24h-Format
    $startLabel24 = BaseHelper::formatDate($session->start_date, 'd.m.Y H:i');
    $endLabel24   = $session->end_date ? BaseHelper::formatDate($session->end_date, 'd.m.Y H:i') : null;
@endphp

<style>
/* ====== Globals ====== */
.checkout-fw .card{ background:#fff; border-radius:10px; box-shadow:0 1px 6px rgba(0,0,0,.06); }
.checkout-fw .card-body{ padding:16px; }
.checkout-fw .form-card{ margin-bottom:24px; }
.checkout-fw .form-card .card-body{ padding:20px; }
.checkout-fw .payment-checkout-btn{ width:100%; }
.checkout-fw .widget-content.shadow-block{ border-radius:10px; }

/* ====== Inspira Ticket ====== */
.inspira-ticket{
  --mint:#578E88;          /* Primärgrün hier anpassen */
  --chip-bg:#F3F3F3;
  --chip-fg:#578E88;
  --ink:#0F172A;
}

.inspira-ticket .ticket{
  --head-h:64px; /* Headerhöhe, wird für Perforation & Loch genutzt */

  display:grid;
  grid-template-columns: 0.95fr 1.15fr 0.68fr; /* Bild | Inhalte | Summen */
  grid-template-rows: var(--head-h) auto;
  grid-template-areas:
    "header header header"
    "left   middle  right";
  border-radius:12px;
  overflow:hidden;
  background:#fff;
  box-shadow:0 8px 24px rgba(0,0,0,.06);
  margin-bottom:24px;
}

/* Headerband über Bild+Mitte */
.inspira-ticket .ticket__header{
  grid-area:header;
  background:var(--mint);
  display:flex; align-items:center;
  padding:0 18px;
  position:relative; z-index:3; /* ganz oben */
}
.inspira-ticket .ticket__logo{
  height:28px; width:auto; display:block;
}

/* Linke Bildspalte – edge to edge */
.inspira-ticket .ticket__left{
  grid-area:left; padding:0; background:#fff;
  position:relative; z-index:2;
}
.inspira-ticket .ticket__left .hero{
  width:100%; height:100%; min-height:240px;
  object-fit:cover; display:block;
}

/* Mittlere Inhaltsfläche (hell) */
.inspira-ticket .ticket__middle{
  grid-area:middle;
  background:#F8FAFB;
  padding:18px 20px;
  position:relative; z-index:2;
}
.inspira-ticket .ttl{
  margin:4px 0 10px;
  font:600 18px/22px system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial;
  color:var(--ink);
}
.inspira-ticket .row{ margin:6px 0; }
.inspira-ticket .row .val{
  font:700 14px/18px system-ui; color:var(--ink);
}
.inspira-ticket .row small{
  display:block; margin-top:2px;
  font:400 12px/14px system-ui; color:#94A3B8;
}

/* Chips */
.inspira-ticket .chips{
  margin-top:12px; display:flex; gap:8px; flex-wrap:wrap;
}
.inspira-ticket .chip{
  background:var(--chip-bg); color:var(--chip-fg);
  font:500 12px/14px system-ui; padding:7px 12px; border-radius:0;
  display:inline-flex; align-items:center; gap:6px; white-space:nowrap;
}

/* Rechte Summenspalte (schwarz) – z-index:0 => ganz unten */
.inspira-ticket .ticket__right{
  grid-area:right; background:#000; color:#fff; padding:18px;
  position:relative; z-index:0;
}
.inspira-ticket .right-title{
  margin:0 0 10px; font:700 16px/20px system-ui; color:#fff;
}
.inspira-ticket .kv{
  display:flex; justify-content:space-between; gap:10px; margin:6px 0;
}
.inspira-ticket .kv span{ color:#C7CED6; font:400 12px/14px system-ui; }
.inspira-ticket .kv b{ color:#fff; font:600 13px/16px system-ui; }
.inspira-ticket .divider{ height:1px; background:rgba(255,255,255,.15); margin:12px 0; }
.inspira-ticket .total{
  display:flex; justify-content:space-between; font:700 18px/22px system-ui; color:#fff;
}

/* ===== Perforation: durch den Header + 2 Löcher (oben/unten) ===== */

/* gestrichelte Linie: über den Header hochziehen, über der schwarzen Spalte liegen */
.inspira-ticket .ticket__right::before{
  content:"";
  position:absolute;
  left:-1px;
  top: calc(-1 * var(--head-h));  /* über das Headerband */
  bottom: 0;
  border-left:2px dashed #fff;
  z-index:2; /* sichtbarer als die schwarze Spalte */
}
/* mittlere Lochdarstellung der alten Variante aus */
.inspira-ticket .ticket__right::after{ display:none; }

/* nur zwei Löcher: ganz oben (über Headerkante) und ganz unten */
.inspira-ticket .ticket__middle::before,
.inspira-ticket .ticket__middle::after{
  content:"";
  position:absolute;
  right:-12px; width:24px; height:24px;
  border-radius:50%; background:#fff;
  box-shadow:0 0 0 1px rgba(0,0,0,.06) inset;
}
/* oberes Loch – bis ganz an die Kartenoberkante */
.inspira-ticket .ticket__middle::before{ top: calc(-1 * var(--head-h) - 12px); }
/* unteres Loch – Kartenende */
.inspira-ticket .ticket__middle::after { bottom:-12px; }

/* Responsive Stack */
@media (max-width: 900px){
  .inspira-ticket .ticket{
    grid-template-columns:1fr;
    grid-template-rows: var(--head-h) auto auto auto;
    grid-template-areas:"header" "left" "middle" "right";
  }
  .inspira-ticket .ticket__right::before,
  .inspira-ticket .ticket__middle::before,
  .inspira-ticket .ticket__middle::after{ display:none; }
}

/* Stacking order (back → front) */
.inspira-ticket .ticket__header {        /* green band */
  position: relative;
  z-index: 0;             /* farthest back */
}

.inspira-ticket .ticket__right {         /* black totals panel */
  position: relative;
  z-index: 1;             /* above header */
}

.inspira-ticket .ticket__left,
.inspira-ticket .ticket__middle {        /* image + details */
  position: relative;
  z-index: 2;             /* above header & totals */
}

/* perforation & holes must sit on top of everything */
.inspira-ticket .ticket__right::before,  /* dashed line */
.inspira-ticket .ticket__middle::before, /* top hole */
.inspira-ticket .ticket__middle::after { /* bottom hole */
  z-index: 3;
}

</style>

<section class="checkout-booking-page checkout-fw">
  <div class="container pt-120 pb-40 checkout-booking">

    {{-- ===== TICKET (Container 1) ===== --}}
    <div class="inspira-ticket">
      <div class="ticket">

        {{-- Headerband (Logo) --}}
        <div class="ticket__header">
          <img class="ticket__logo"
               src="{{ Theme::asset()->url('images/inspira-logo-light.svg') }}"
               alt="Inspira">
        </div>

        {{-- Linke Bildfläche --}}
        <div class="ticket__left">
          <img class="hero"
               src="{{ RvMedia::getImageUrl($course->thumbnail, default: RvMedia::getDefaultImage()) }}"
               alt="{{ $course->name }}">
        </div>

        {{-- Mitte: Titel, Start/Ende, Chips --}}
        <div class="ticket__middle">
          <h3 class="ttl">{{ $course->name }}</h3>

          <div class="row">
            <div class="val">{{ $startLabel24 }}</div>
            <small>{{ __('Startdatum der Sitzung') }}</small>
          </div>

          @if($endLabel24)
            <div class="row">
              <div class="val">{{ $endLabel24 }}</div>
              <small>{{ __('Enddatum der Sitzung') }}</small>
            </div>
          @endif

          <div class="chips">
            @if($course->duration)
              <span class="chip">{{ __('Dauer:') }} {{ $course->duration }}</span>
            @endif
            @if($course->category)
              <span class="chip">{{ $course->category->name }}</span>
            @endif
            @isset($amount)
              <span class="chip">{{ format_price($amount) }}</span>
            @endisset
          </div>
        </div>

        {{-- Rechts: Summenblock (liegt z-index-technisch unten) --}}
        <div class="ticket__right">
          <div class="right-title">{{ __('Gesamtpreis') }}</div>

          <div class="kv">
            <span>{{ __('Preis') }}</span>
            <b class="amount-text">{{ format_price($amount) }}</b>
          </div>
          <div class="kv">
            <span>{{ __('Rabatt') }}</span>
            <b class="discount-text">{{ format_price($couponAmount) }}</b>
          </div>
          <div class="kv">
            <span>{{ __('Steuern') }}</span>
            <b class="tax-text">{{ format_price($taxAmount) }}</b>
          </div>

          <div class="divider"></div>

          <div class="total">
            <span>{{ __('Gesamt') }}</span>
            <span class="total-amount-text">{{ format_price($total) }}</span>
          </div>
        </div>

      </div>
    </div>

    {{-- ===== FORMULAR (Container 2) ===== --}}
    <div class="card form-card shadow-block">
      <div class="card-body">
        <form action="{{ route('public.course.booking.checkout') }}" class="booking-form-main payment-checkout-form" method="POST">
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

          @if (! $customer->id)
            <p>{{ __('Already have an account?') }} <a href="{{ route('customer.login') }}">{{ __(' Login') }}</a></p>
          @endif

          <h3 class="mb-20">{{ __('Your Information') }}</h3>

          <div class="room-booking-form p-0">
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
                  <label for="txt-country">{{ __('State / Province') }}</label>
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
                <div class="row d-none form-create-customer-password">
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

            <div class="form-group mb-0">
              <button type="submit" class="btn btn-filled payment-checkout-btn"
                      data-processing-text="{{ __('Processing. Please wait...') }}"
                      data-error-header="{{ __('Error') }}">{{ __('Checkout') }}</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    {{-- ===== STORNO (Container 3) ===== --}}
    @if ($cancellation = theme_option('cancellation'))
      <div class="widget-content shadow-block">
        <h3 class="mb-20">{{ __('Cancellation') }}</h3>
        {!! BaseHelper::clean($cancellation) !!}
      </div>
    @endif

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
