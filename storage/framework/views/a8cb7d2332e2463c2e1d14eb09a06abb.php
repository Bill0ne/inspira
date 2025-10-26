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
    Theme::asset()->container('footer')->usePath()->add('checkout-js', 'js/checkout.js');

    // <<< NEU: Login-Status für Preis-Anzeige >>>
    $isLoggedIn = auth('customer')->check() || auth()->check();
?>


<style>
    .checkout-booking-page .row {
        display: flex !important;
        flex-wrap: wrap !important;
    }
    
    .checkout-booking-page .col-lg-8 {
        flex: 0 0 66.666667% !important;
        max-width: 66.666667% !important;
    }
    
    .checkout-booking-page .col-lg-4 {
        flex: 0 0 33.333333% !important;
        max-width: 33.333333% !important;
    }
    
    /* Verhindere dass externe CSS die Order ändert */
    .checkout-booking-page .col-lg-8,
    .checkout-booking-page .col-lg-4 {
        float: none !important;
    }
</style>

<section class="checkout-booking-page">
    
</section>

<section class="checkout-booking-page">
    <div class="container pt-120 pb-40 checkout-booking">
        <div class="row">
            <div class="col-lg-8 col-md-12 col-sm-12">
                <form action="<?php echo e(route('public.booking.checkout')); ?>" class="booking-form-main payment-checkout-form mb-50 shadow-block" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="token" value="<?php echo e($token); ?>">
                    <input type="hidden" name="amount" value="<?php echo e($total); ?>">
                    <input type="hidden" name="room_id" value="<?php echo e($room->id); ?>">
                    <?php $__currentLoopData = $slotSummaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <input type="hidden" name="slots[<?php echo e($i); ?>][start_date]" value="<?php echo e($s['start_date']->format(HotelHelper::getDateFormat())); ?>">
                        <input type="hidden" name="slots[<?php echo e($i); ?>][end_date]"   value="<?php echo e($s['end_date']->format(HotelHelper::getDateFormat())); ?>">
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <input type="hidden" name="adults" value="<?php echo e($adults); ?>">
                    <input name="number_of_children" type="hidden" value="<?php echo e($children); ?>">
                    <input name="rooms" type="hidden" value="<?php echo e($rooms); ?>"/>
                    <input type="hidden" name="currency" value="<?php echo e(strtoupper(get_application_currency()->title)); ?>">
                    <input type="hidden" name="currency_id" value="<?php echo e(get_application_currency_id()); ?>">
                    <?php if(is_plugin_active('paypal')): ?>
                        <input type="hidden" name="callback_url" value="<?php echo e(route('payments.paypal.status')); ?>">
                    <?php endif; ?>

                    <input type="hidden" name="number_of_guests" value="<?php echo e($adults); ?>">

                    <?php if(! $customer->id): ?>
                        <p><?php echo e(__('Already have an account?')); ?> <a href="<?php echo e(route('customer.login')); ?>"><?php echo e(__(' Login')); ?></a></p>
                    <?php endif; ?>

                    <div class="mb-20">
                        <h3 class=""><?php echo e(__('Add Extra Services')); ?></h3>
                    </div>
                    <div class="room-booking-form p-0 mb-20">
                        <?php
                            $chunks = $services->chunk(ceil($services->count() / 2));
                        ?>
                        <div class="row">
                            <?php if(count($chunks) > 0): ?>
                                <div class="col-md-6">
                                    <?php $__currentLoopData = $chunks[0]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="form-group mb-20 custom-checkbox">
                                            <label for="service_<?php echo e($service->id); ?>">
                                                <input type="checkbox" class="service-item" id="service_<?php echo e($service->id); ?>" name="services[]" value="<?php echo e($service->id); ?>" <?php if(in_array($service->id, (array)old('services', $selectedServices))): ?> checked <?php endif; ?>>
                                                <?php echo e($service->name); ?>

                                                <em>(
                                                    <?php echo e($isLoggedIn ? format_price($service->price) : __('Preis nach Login')); ?>

                                                )</em>
                                                <span></span>
                                            </label>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                            <?php if(count($chunks) > 1): ?>
                                <div class="col-md-6">
                                    <?php $__currentLoopData = $chunks[1]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="form-group mb-20 custom-checkbox">
                                            <label for="service_<?php echo e($service->id); ?>">
                                                <input type="checkbox" class="service-item" id="service_<?php echo e($service->id); ?>" name="services[<?php echo e($service->id); ?>]" value="<?php echo e($service->id); ?>" <?php if(in_array($service->id, (array)old('services', $selectedServices))): ?> checked <?php endif; ?>>
                                                <?php echo e($service->name); ?>

                                                <em>(
                                                    <?php echo e($isLoggedIn ? format_price($service->price) : __('Preis nach Login')); ?>

                                                )</em>
                                                <span></span>
                                            </label>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if(count($foods) > 0): ?>
                        <div class="mb-20">
                            <h3 class=""><?php echo e(__('Add Foods')); ?></h3>
                        </div>

                        <div class="room-booking-form p-0 mb-20">
                            <div class="row">
                                <?php $__currentLoopData = $foods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $food): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-6">
                                        <div class="form-group mb-20 custom-checkbox">
                                            <label for="food_<?php echo e($food->id); ?>">
                                                <input type="checkbox" class="food-item" id="food_<?php echo e($food->id); ?>" name="foods[]" value="<?php echo e($food->id); ?>" <?php if(in_array($food->id, (array)old('foods', $selectedFoods))): ?> checked <?php endif; ?>>
                                                <?php echo e($food->name); ?>

                                                <em>(
                                                    <?php echo e($isLoggedIn ? format_price($food->price) : __('Preis nach Login')); ?>

                                                )</em>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
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
                            <div class="col-md-6">
                                <div class="form-group left-icon mb-20">
                                    <label for="arrival_time"><?php echo e(__('Arrival Time')); ?></label>
                                    <select name="arrival_time" id="arrival_time" class="form-select">
                                        <option><?php echo e(__('I do not know')); ?></option>
                                        <option>12:00 - 1:00 <?php echo e(__('AM')); ?></option>
                                        <option>1:00 - 2:00 <?php echo e(__('AM')); ?></option>
                                        <option>2:00 - 3:00 <?php echo e(__('AM')); ?></option>
                                        <option>3:00 - 4:00 <?php echo e(__('AM')); ?></option>
                                        <option>4:00 - 5:00 <?php echo e(__('AM')); ?></option>
                                        <option>5:00 - 6:00 <?php echo e(__('AM')); ?></option>
                                        <option>6:00 - 7:00 <?php echo e(__('AM')); ?></option>
                                        <option>7:00 - 8:00 <?php echo e(__('AM')); ?></option>
                                        <option>8:00 - 9:00 <?php echo e(__('AM')); ?></option>
                                        <option>9:00 - 10:00 <?php echo e(__('AM')); ?></option>
                                        <option>10:00 - 11:00 <?php echo e(__('AM')); ?></option>
                                        <option>11:00 - 12:00 <?php echo e(__('AM')); ?></option>
                                        <option>12:00 - 1:00 <?php echo e(__('PM')); ?></option>
                                        <option>1:00 - 2:00 <?php echo e(__('PM')); ?></option>
                                        <option>2:00 - 3:00 <?php echo e(__('PM')); ?></option>
                                        <option>3:00 - 4:00 <?php echo e(__('PM')); ?></option>
                                        <option>4:00 - 5:00 <?php echo e(__('PM')); ?></option>
                                        <option>5:00 - 6:00 <?php echo e(__('PM')); ?></option>
                                        <option>6:00 - 7:00 <?php echo e(__('PM')); ?></option>
                                        <option>7:00 - 8:00 <?php echo e(__('PM')); ?></option>
                                        <option>8:00 - 9:00 <?php echo e(__('PM')); ?></option>
                                        <option>9:00 - 10:00 <?php echo e(__('PM')); ?></option>
                                        <option>10:00 - 11:00 <?php echo e(__('PM')); ?></option>
                                        <option>11:00 - 12:00 <?php echo e(__('PM')); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php if(! $customer->id): ?>
                            <div class="create-customer">
                                <div class="row">
                                    <div class="form-group mb-20 custom-checkbox d-block">
                                        <label for="register-customer" class="w-100">
                                            <input type="checkbox" id="register-customer" name="register_customer" value="1" > <?php echo e(__('Register an account with above information?')); ?>

                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="row d-none form-create-customer-password">
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

                        <?php echo $__env->make('plugins/hotel::coupons.partials.form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                        <?php if(is_plugin_active('payment') && ($defaultPaymentMethod = PaymentMethods::getDefaultMethod()) && get_payment_setting('status', $defaultPaymentMethod)): ?>
                            <div class="form-group mb-20">
                                <label for="requests"><?php echo e(__('Payment method')); ?></label>
                                <ul class="list-group list_payment_method">
                                    <?php echo apply_filters(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, null, [
                                        // Die Übergabe enthält weiterhin den Gesamtbetrag,
                                        // sichtbare Preise steuern wir im Sidebar-Block unten.
                                        'amount' => $total,
                                        'currency' => strtoupper(get_application_currency()->title),
                                        'name' => $room->name,
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
                                <input type="checkbox" id="terms_conditions" name="terms_conditions" value="1" <?php if(old('terms_conditions') == 1): ?> checked <?php endif; ?>> <?php echo e(__('Terms & conditions *')); ?>

                                <span></span>
                            </label>
                        </div>
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-filled payment-checkout-btn" data-processing-text="<?php echo e(__('Processing. Please wait...')); ?>" data-error-header="<?php echo e(__('Error')); ?>"><?php echo e(__('Checkout')); ?></button>
                        </div>
                    </div>
                </form>

                <?php if($hotelRules = theme_option('hotel_rules')): ?>
                    <div class="widget-content mb-50 hotel-rules shadow-block">
                        <h3 class="mb-20"><?php echo e(__('Hotel rules')); ?></h3>
                        <?php echo BaseHelper::clean($hotelRules); ?>

                    </div>
                <?php endif; ?>

                <?php if( $cancellation = theme_option('cancellation')): ?>
                    <div class="widget-content mb-50 shadow-block">
                        <h3 class="mb-20"><?php echo e(__('Cancellation')); ?></h3>
                        <?php echo BaseHelper::clean($cancellation); ?>

                    </div>
                <?php endif; ?>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-4 sidebar">
                <aside>
                    <div class="wrap">
                        <img src="<?php echo e(RvMedia::getImageUrl($room->image, default: RvMedia::getDefaultImage())); ?>" alt="<?php echo e($room->name); ?>">

                        <div class="room-information">
                            <span><?php echo e($room->name); ?></span>
                        </div>
                    </div>

                    <div class="form-information text-white">
                        <p class="text-center fw-bold text-uppercase"><?php echo e(__('Your Reservation')); ?></p>
                        <div>
                            
                            <div class="mt-3 p-3 border rounded">
                                <h5 class="text-warning mb-3"><?php echo e(__('Your Selected Periods')); ?></h5>

                                <?php if(!empty($slotSummaries)): ?>
                                    <ul class="list-unstyled mb-0">
                                        <?php $__currentLoopData = $slotSummaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $start = $slot['start_date'];
                                                $end = $slot['end_date'];
                                            ?>
                                            <li class="mb-2">

                                                <?php if($start->isSameDay($end)): ?>
                                                    
                                                    <?php echo e($start->format('d M Y')); ?>

                                                    <span class="text-gray-500 mx-1"></span>
                                                    <?php echo e($start->format('h:i A')); ?> – <?php echo e($end->format('h:i A')); ?>

                                                <?php else: ?>
                                                    
                                                    <?php echo e($start->format('d M Y h:i A')); ?> → <?php echo e($end->format('d M Y h:i A')); ?>

                                                <?php endif; ?>

                                                <br>
                                                    <span class="fw-bold text-warning small">
    <span class="text-light fw-normal"><?php echo e(__('Price')); ?>:</span> <?php echo e(format_price($slot['final_price'])); ?>

</span>

                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                <?php else: ?>
                                    
                                    <p><?php echo e(__('Check-In')); ?>: <?php echo e($displayStart ? $displayStart->translatedFormat('l, d M, Y') : '-'); ?></p>
                                    <p><?php echo e(__('Check-Out')); ?>: <?php echo e($displayEnd ? $displayEnd->translatedFormat('l, d M, Y') : '-'); ?></p>
                                <?php endif; ?>
                            </div>


                            <p><?php echo e(__('Number of rooms')); ?>: <?php echo e($rooms); ?></p>
                            <p><?php echo e(__('Number of adults')); ?>: <?php echo e($adults); ?></p>
                            <p><?php echo e(__('Number of children')); ?>: <?php echo e($children); ?></p>

                            <?php
                                $priceDifference = $totalBasePrice - $totalAmount;
                            ?>

                            
                            <?php if($totalAmount < $totalBasePrice): ?>
                                <div class="kv">
                                    <span class="text-light"><?php echo e(__('Original Price')); ?></span>
                                    <b class="text-light text-decoration-line-through opacity-75"><?php echo e(format_price($totalBasePrice)); ?></b>
                                </div>
                                <div class="kv">
                                    <span class="text-light"><?php echo e(__('Discounted Price')); ?></span>
                                    <b class="fw-bold text-warning amount-text"><?php echo e(format_price($totalAmount)); ?></b>
                                </div>
                                <div class="kv small mt-1">
                                    <i class="fas fa-tag me-1 text-warning"></i>
                                    <span class="text-warning">
                            <?php echo e(__('You save :amount', ['amount' => format_price(abs($priceDifference))])); ?>

                        </span>
                                </div>
                            <?php else: ?>
                                <div class="kv">
                                    <span class="text-light"><?php echo e(__('Price')); ?></span>
                                    <b class="fw-bold text-warning amount-text"><?php echo e(format_price($totalAmount)); ?></b>
                                </div>
                            <?php endif; ?>

                            
                            <div class="kv">
                                <span class="text-light"><?php echo e(__('Discount (Coupon)')); ?></span>
                                <b class="text-warning discount-text"><?php echo e(format_price($couponAmount)); ?></b>
                            </div>

                            
                            <div class="kv">
                                <span class="text-light"><?php echo e(__('Tax')); ?></span>
                                <b class="text-warning tax-text"><?php echo e(format_price($taxAmount)); ?></b>
                            </div>

                            <hr class="border-light opacity-50">

                            
                            <div class="kv total">
                                <span class="text-light fw-bold"><?php echo e(__('Total')); ?></span>
                                <span class="total-amount-text fw-bold text-white"><?php echo e(format_price($total)); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="text-center footer">
                        <p><?php echo e(__('Total')); ?>:
                            <span class="total-amount-text">
                    <?php echo e($isLoggedIn ? format_price($total) : __('Preis nach Login')); ?>

                </span>
                        </p>
                    </div>
                </aside>
            </div>

        </div>
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
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/views/hotel/booking.blade.php ENDPATH**/ ?>