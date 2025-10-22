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

    // Normalize customer (may be null)
    $customer = $customer ?? auth('customer')->user();

    // Labels im 24h-Format
    $startLabel24 = BaseHelper::formatDate($session->start_date, 'd.m.Y H:i');
    $endLabel24   = $session->end_date ? BaseHelper::formatDate($session->end_date, 'd.m.Y H:i') : null;
@endphp

<style>
/* ===== Global ===== */
.checkout-fw .card{ background:#fff; border-radius:10px; box-shadow:0 1px 6px rgba(0,0,0,.06); }
.checkout-fw .card-body{ padding:16px; }
.checkout-fw .form-card{ margin-bottom:24px; }
.checkout-fw .form-card .card-body{ padding:20px; }
.checkout-fw .payment-checkout-btn{ width:100%; }
.checkout-fw .widget-content.shadow-block{ border-radius:10px; }

/* ===== Ticket – kantig, minimal, farbige Bänder ===== */
.checkout-fw :root{ --mint:#578E88; --ink:#111827; }

.ticket{
  display:grid;
  grid-template-columns: 1.15fr 1.35fr 1fr;  /* Bild | Details | Summen */
  border-radius:8px;
  overflow:hidden;
  box-shadow:0 4px 18px rgba(0,0,0,.06);
  margin-bottom:24px;
  background:#fff;
}
@media (max-width: 767.98px){ .ticket{ grid-template-columns:1fr; } }

.ticket__col{ padding:16px 18px; }

/* Bild: edge-to-edge */
.ticket__media{ padding:0; position:relative; background:#fff; }
.ticket__media img{
  width:100%; height:100%;
  min-height:240px;
  object-fit:cover; display:block; border-radius:0;
}

/* Mitte: Primärgrün + weiße gestrichelte Linie links */
.ticket__details{
  position:relative;
  background:var(--mint);
  color:#F3F3F3;
}
.ticket__details::before{
  content:""; position:absolute; left:0; top:0; bottom:0;
  border-left:2px dashed rgba(255,255,255,.95);
}

/* Rechts: Schwarz */
.ticket__totals{ background:#000; color:#fff; }

/* Typo kompakt */
.ticket .title{ margin:0 0 10px; font-weight:600; font-size:16px; }
.ticket .kv{
  display:flex; justify-content:space-between; align-items:flex-start;
  gap:12px; margin:6px 0; font-size:13px; line-height:18px;
}
.ticket .kv b{ font-weight:600; }
.ticket .total{ margin-top:10px; font-size:18px; font-weight:700; }
.ticket__totals hr{ border:0; height:1px; background:rgba(255,255,255,.12); margin:10px 0; }

/* ==== FARBFIX – erzwinge Mittelfarbe + helle Typo ==== */
.checkout-fw .ticket .ticket__details{
  background: var(--mint, #578E88) !important;
  background-color: var(--mint, #578E88) !important;
  color:#F3F3F3 !important;
}
.checkout-fw .ticket .ticket__details,
.checkout-fw .ticket .ticket__details *{
  color:#F3F3F3 !important;
}
.checkout-fw .ticket .ticket__details .title{ color:#F3F3F3 !important; }
</style>

<section class="checkout-booking-page checkout-fw">
  <div class="container pt-120 pb-40 checkout-booking">

    {{-- ===== Container 1: Ticket ===== --}}
    <div class="ticket">
      {{-- Bild --}}
      <div class="ticket__col ticket__media">
        <img
          src="{{ RvMedia::getImageUrl($course->thumbnail, default: RvMedia::getDefaultImage()) }}"
          alt="{{ $course->name }}"
        >
      </div>

      {{-- Details (Mint) --}}
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

      {{-- Summen (Schwarz) --}}
      <div class="ticket__col ticket__totals">
        <h5 class="title">{{ __('Gesamtpreis') }}</h5>

        {{-- Show configurator discount if applicable --}}
        @if($discountAmount > 0)
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
            {{ __('You save :amount', ['amount' => format_price($discountAmount)]) }}
          </div>
        @else
          <div class="kv">
            <span>{{ __('Preis') }}</span>
            <b class="amount-text">{{ format_price($amount) }}</b>
          </div>
        @endif

        {{-- Coupon discount --}}
        <div class="kv">
          <span>{{ __('Rabatt (Coupon)') }}</span>
          <b class="discount-text">{{ format_price($couponAmount) }}</b>
        </div>

        {{-- Tax --}}
        <div class="kv">
          <span>{{ __('Steuern') }}</span>
          <b class="tax-text">{{ format_price($taxAmount) }}</b>
        </div>

        <hr>

        {{-- Total --}}
        <div class="kv total">
          <span>{{ __('Gesamt') }}</span>
          <span class="total-amount-text fw-bold">{{ format_price($total) }}</span>
        </div>
      </div>
    </div>

    {{-- ===== Container 2: Formular ===== --}}
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

          @if (! auth('customer')->check())
            <p>{{ __('Already have an account?') }}
              <a href="{{ route('customer.login') }}">{{ __(' Login') }}</a></p>
          @endif

          <h3 class="mb-20">{{ __('Your Information') }}</h3>

          <div class="room-booking-form p-0">
            <p class="mb-20">{{ __('Required fields are followed by *') }}</p>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <label for="txt-first-name">{{ __('First Name') }} <span class="required">*</span></label>
                  <input type="text" name="first_name" id="txt-first-name" class="form-control" required
                         value="{{ old('first_name', $customer?->first_name) }}">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <label for="txt-last-name">{{ __('Last Name') }} <span class="required">*</span></label>
                  <input type="text" name="last_name" id="txt-last-name" class="form-control" required
                         value="{{ old('last_name', $customer?->last_name) }}">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <label for="txt-email">{{ __('Email') }} <span class="required">*</span></label>
                  <input type="email" name="email" id="txt-email" class="form-control" required
                         value="{{ old('email', $customer?->email) }}">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <label for="txt-phone">{{ __('Phone') }} <span class="required">*</span></label>
                  <input type="text" name="phone" id="txt-phone" class="form-control" required
                         value="{{ old('phone', $customer?->phone) }}">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <label for="txt-country">{{ __('Country') }}</label>
                  <input type="text" name="country" id="txt-country" class="form-control"
                         value="{{ old('country', $customer?->country) }}">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <label for="txt-state">{{ __('State / Province') }}</label>
                  <input type="text" name="state" id="txt-state" class="form-control"
                         value="{{ old('state', $customer?->state) }}">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <label for="txt-city">{{ __('City') }}</label>
                  <input type="text" name="city" id="txt-city" class="form-control"
                         value="{{ old('city', $customer?->city) }}">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <label for="txt-address">{{ __('Address') }}</label>
                  <input type="text" name="address" id="txt-address" class="form-control"
                         value="{{ old('address', $customer?->address) }}">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <label for="txt-zip">{{ __('Postal / Zip code') }}</label>
                  <input type="text" name="zip" id="txt-zip" class="form-control"
                         value="{{ old('zip', $customer?->zip) }}">
                </div>
              </div>
            </div>

            @if (! auth('customer')->check())
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

    {{-- ===== Container 3: Storno ===== --}}
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
