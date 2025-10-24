<?php if(is_plugin_active('payment')): ?>
    <link rel="stylesheet" href="<?php echo e(asset('vendor/core/plugins/payment/css/payment.css')); ?>?v=1.0.3">
    <?php
        Theme::asset()->container('header')->usePath()->add('jquery', 'plugins/jquery.min.js');
        Theme::asset()->container('header')->add('payment-js', 'vendor/core/plugins/payment/js/payment.js');
    ?>
    <?php echo apply_filters(PAYMENT_FILTER_HEADER_ASSETS, null); ?>

<?php endif; ?>

<?php
    Theme::set('pageTitle', __('Booking'));
    Theme::asset()->container('footer')->usePath()->add('checkout-js', 'js/course-checkout.js');

    // Labels im 24h-Format
    $startLabel24 = BaseHelper::formatDate($session->start_date, 'd.m.Y H:i');
    $endLabel24   = $session->end_date ? BaseHelper::formatDate($session->end_date, 'd.m.Y H:i') : null;
?>

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

/* ==== FARBFIX – erzwinge Mittelfarbe + helle Typo (gegen Theme-Overrides) ==== */
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

    
    <div class="ticket">
      
      <div class="ticket__col ticket__media">
        <img
          src="<?php echo e(RvMedia::getImageUrl($course->thumbnail, default: RvMedia::getDefaultImage())); ?>"
          alt="<?php echo e($course->name); ?>"
        >
      </div>

      
      <div class="ticket__col ticket__details">
        <h5 class="title"><?php echo e(__('Ihre Reservierung')); ?></h5>
        <div class="kv"><span><?php echo e($course->name); ?></span></div>
        <div class="kv">
          <span><?php echo e(__('Startdatum der Sitzung')); ?></span>
          <b class="session-start-date"><?php echo e($startLabel24); ?></b>
        </div>
        <?php if($endLabel24): ?>
          <div class="kv">
            <span><?php echo e(__('Enddatum der Sitzung')); ?></span>
            <b class="session-end-date"><?php echo e($endLabel24); ?></b>
          </div>
        <?php endif; ?>
      </div>

      
      <div class="ticket__col ticket__totals">
        <h5 class="title"><?php echo e(__('Gesamtpreis')); ?></h5>

        <?php
          // Base & dynamic price difference
          $priceDifference = $basePrice - $amount;
        ?>

        
        <?php if($amount < $basePrice): ?>
          
          <div class="kv">
            <span><?php echo e(__('Originalpreis')); ?></span>
            <b class="text-muted text-decoration-line-through"><?php echo e(format_price($basePrice)); ?></b>
          </div>
          <div class="kv">
            <span><?php echo e(__('Rabattierter Preis')); ?></span>
            <b class="text-success fw-bold amount-text"><?php echo e(format_price($amount)); ?></b>
          </div>
          <div class="kv small text-success mt-1">
            <i class="fas fa-tag me-1"></i>
            <?php echo e(__('You save :amount', ['amount' => format_price(abs($priceDifference))])); ?>

          </div>
        <?php else: ?>
          
          <div class="kv">
            <span><?php echo e(__('Preis')); ?></span>
            <b class="amount-text"><?php echo e(format_price($amount)); ?></b>
          </div>
        <?php endif; ?>

        
        <div class="kv">
          <span><?php echo e(__('Rabatt (Coupon)')); ?></span>
          <b class="discount-text"><?php echo e(format_price($couponAmount)); ?></b>
        </div>

        
        <div class="kv">
          <span><?php echo e(__('Steuern')); ?></span>
          <b class="tax-text"><?php echo e(format_price($taxAmount)); ?></b>
        </div>

        <hr>

        
        <div class="kv total">
          <span><?php echo e(__('Gesamt')); ?></span>
          <span class="total-amount-text fw-bold"><?php echo e(format_price($total)); ?></span>
        </div>
      </div>



    </div>

    
    <div class="card form-card shadow-block">
      <div class="card-body">
        <form action="<?php echo e(route('public.course.booking.checkout')); ?>" class="booking-form-main payment-checkout-form" method="POST">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="token" value="<?php echo e($token); ?>">
          <input type="hidden" name="amount" value="<?php echo e($total); ?>">
          <input type="hidden" name="course_id" value="<?php echo e($course->id); ?>">
          <input type="hidden" name="session_id" value="<?php echo e($session->id); ?>">
          <input type="hidden" name="currency" value="<?php echo e(strtoupper(get_application_currency()->title)); ?>">
          <input type="hidden" name="currency_id" value="<?php echo e(get_application_currency_id()); ?>">
          <?php if(is_plugin_active('paypal')): ?>
            <input type="hidden" name="callback_url" value="<?php echo e(route('payments.paypal.status')); ?>">
          <?php endif; ?>

          <?php if(! $customer->id): ?>
            <p><?php echo e(__('Already have an account?')); ?> <a href="<?php echo e(route('customer.login')); ?>"><?php echo e(__(' Login')); ?></a></p>
          <?php endif; ?>

          <h3 class="mb-20"><?php echo e(__('Your Information')); ?></h3>

          <div class="room-booking-form p-0">
            <p class="mb-20"><?php echo e(__('Required fields are followed by *')); ?></p>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <label for="txt-first-name"><?php echo e(__('First Name')); ?> <span class="required">*</span></label>
                  <input type="text" name="first_name" id="txt-first-name" class="form-control" required value="<?php echo e(old('first_name', $customer)); ?>">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <label for="txt-last-name"><?php echo e(__('Last Name')); ?> <span class="required">*</span></label>
                  <input type="text" name="last_name" id="txt-last-name" class="form-control" required value="<?php echo e(old('last_name', $customer)); ?>">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <label for="txt-email"><?php echo e(__('Email')); ?> <span class="required">*</span></label>
                  <input type="email" name="email" id="txt-email" class="form-control" required value="<?php echo e(old('email', $customer)); ?>">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <label for="txt-phone"><?php echo e(__('Phone')); ?> <span class="required">*</span></label>
                  <input type="text" name="phone" id="txt-phone" class="form-control" required value="<?php echo e(old('phone', $customer)); ?>">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <label for="txt-country"><?php echo e(__('Country')); ?></label>
                  <input type="text" name="country" id="txt-country" class="form-control" value="<?php echo e(old('country', $customer)); ?>">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <label for="txt-country"><?php echo e(__('State / Province')); ?></label>
                  <input type="text" name="state" id="txt-state" class="form-control" value="<?php echo e(old('state', $customer)); ?>">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <label for="txt-city"><?php echo e(__('City')); ?></label>
                  <input type="text" name="city" id="txt-city" class="form-control" value="<?php echo e(old('city', $customer)); ?>">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <label for="txt-address"><?php echo e(__('Address')); ?></label>
                  <input type="text" name="address" id="txt-address" class="form-control" value="<?php echo e(old('address', $customer)); ?>">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <label for="txt-zip"><?php echo e(__('Postal / Zip code')); ?></label>
                  <input type="text" name="zip" id="txt-zip" class="form-control" value="<?php echo e(old('zip', $customer)); ?>">
                </div>
              </div>
            </div>

            <?php if(! $customer->id): ?>
              <div class="create-customer">
                <div class="row">
                  <div class="form-group mb-20 custom-checkbox d-block">
                    <label for="register-customer" class="w-100">
                      <input type="checkbox" id="register-customer" name="register_customer" value="1">
                      <?php echo e(__('Register an account with above information?')); ?>

                      <span></span>
                    </label>
                  </div>
                </div>
                <div class="row form-create-customer-password">
                  <div class="col-md-6">
                    <div class="form-group mb-20">
                      <label for="password"><?php echo e(__('Password')); ?></label>
                      <input type="password" name="password" id="password" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group mb-20">
                      <label for="password_confirmation"><?php echo e(__('Password confirm')); ?></label>
                      <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>

            <div class="form-group mb-20">
              <label for="requests"><?php echo e(__('Requests')); ?></label>
              <textarea name="requests" rows="3" class="form-control" id="requests" placeholder="<?php echo e(__('Write Something')); ?>..."><?php echo e(old('requests')); ?></textarea>
            </div>

            <?php echo $__env->make('plugins/courses::coupons.partials.form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php if(is_plugin_active('payment') && ($defaultPaymentMethod = PaymentMethods::getDefaultMethod()) && get_payment_setting('status', $defaultPaymentMethod)): ?>
              <div class="form-group mb-20">
                <label><?php echo e(__('Payment method')); ?></label>
                <ul class="list-group list_payment_method">
                  <?php echo apply_filters(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, null, [
                      'amount' => $total,
                      'currency' => strtoupper(get_application_currency()->title),
                      'name' => $course->name,
                      'selected' => PaymentMethods::getSelectedMethod(),
                      'default' => $defaultPaymentMethod,
                      'selecting' => PaymentMethods::getSelectingMethod(),
                  ]); ?>

                  <?php echo PaymentMethods::render(); ?>

                </ul>
              </div>
            <?php endif; ?>

            <?php echo apply_filters('form_extra_fields_render', null); ?>


            <div class="form-group mb-20 custom-checkbox d-block">
              <label for="terms_conditions" class="w-100">
                <input type="checkbox" id="terms_conditions" name="terms_conditions" value="1" <?php if(old('terms_conditions') == 1): ?> checked <?php endif; ?>>
                <?php echo e(__('Terms & conditions *')); ?> <span></span>
              </label>
            </div>

            <div class="form-group mb-0">
              <button type="submit" class="btn btn-filled payment-checkout-btn"
                      data-processing-text="<?php echo e(__('Processing. Please wait...')); ?>"
                      data-error-header="<?php echo e(__('Error')); ?>"><?php echo e(__('Checkout')); ?></button>
            </div>
          </div>
        </form>
      </div>
    </div>

    
    <?php if($cancellation = theme_option('cancellation')): ?>
      <div class="widget-content shadow-block">
        <h3 class="mb-20"><?php echo e(__('Cancellation')); ?></h3>
        <?php echo BaseHelper::clean($cancellation); ?>

      </div>
    <?php endif; ?>

  </div>
</section>

<?php if(is_plugin_active('payment')): ?>
  <?php echo apply_filters(PAYMENT_FILTER_FOOTER_ASSETS, null); ?>

  <?php
      Theme::asset()->container('footer')
          ->add('js-validation', 'vendor/core/core/js-validation/js/js-validation.js', ['jquery'])
          ->writeContent('checkout-validator', JsValidator::formRequest(Botble\Hotel\Http\Requests\CheckoutRequest::class))
  ?>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/views/courses/booking.blade.php ENDPATH**/ ?>