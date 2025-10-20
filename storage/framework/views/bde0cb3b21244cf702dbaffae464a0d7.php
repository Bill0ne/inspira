<?php
    Theme::set('pageTitle', __('Register'));
?>

<section class="about-area about-p pt-60 pb-60 p-relative fix">
    <div class="container">
        <div class="row flex-row-reverse justify-content-center align-items-center">
            <?php if($backgroundImage = theme_option('authentication_register_background_image')): ?>
                <div class="col-lg-6 col-md-6">
                    <div class="booking-img">
                        <img src="<?php echo e(RvMedia::getImageURL($backgroundImage)); ?>" alt="<?php echo e(__('Register')); ?>" />
                    </div>
                </div>
            <?php endif; ?>

            <div class="col-xxl-5 col-xl-12 col-lg-12">
                <div class="form-border-box">
                    <form class="form-horizontal" role="form" method="POST" action="<?php echo e(route('customer.register.post')); ?>">
                        <h1 class="normal mb-20"><?php echo e(__('Register')); ?></h1>
                        <?php echo csrf_field(); ?>
                        <div class="form-field-wrapper form-group">
                            <div class="col-lg-12 col-md-12">
                                <div class="input-md form-full-width contact-field p-relative c-name mb-20">
                                    <label for="first_name" class="custom-authentication-label">
                                        <span><?php echo e(__('First Name')); ?></span>
                                    </label>
                                    <input class="custom-authentication-input<?php echo e($errors->has('first_name') ? ' is-invalid' : ''); ?>" type="text" id="first_name" name="first_name" placeholder="<?php echo e(__('First Name')); ?>" required value="<?php echo e(BaseHelper::stringify(old('first_name'))); ?>" />
                                    <?php echo Form::error('first_name', $errors); ?>

                                </div>
                            </div>
                        </div>

                        <div class="form-field-wrapper form-group">
                            <div class="col-lg-12 col-md-12">
                                <div class="input-md form-full-width contact-field p-relative c-name mb-20">
                                    <label class="custom-authentication-label" for="last_name">
                                        <span><?php echo e(__('Last Name')); ?></span>
                                    </label>
                                    <input class="custom-authentication-input<?php echo e($errors->has('last_name') ? ' is-invalid' : ''); ?>" type="text" id="last_name" name="last_name" placeholder="<?php echo e(__('Last Name')); ?>" required value="<?php echo e(BaseHelper::stringify(old('last_name'))); ?>" />
                                    <?php echo Form::error('last_name', $errors); ?>

                                </div>
                            </div>
                        </div>

                        <?php echo apply_filters('hotel_customer_register_form_before', null); ?>


                        <div class="form-field-wrapper form-group">
                            <div class="col-lg-12 col-md-12">
                                <div class="input-md form-full-width contact-field p-relative c-name mb-20">
                                    <label class="custom-authentication-label" for="email">
                                        <span><?php echo e(__('Email')); ?></span>
                                    </label>
                                    <input class="custom-authentication-input<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" type="email" id="email" name="email" placeholder="<?php echo e(__('Enter your email')); ?>" required value="<?php echo e(BaseHelper::stringify(old('email'))); ?>" />
                                    <?php echo Form::error('email', $errors); ?>

                                </div>
                            </div>
                        </div>

                        <div class="form-field-wrapper form-group">
                            <div class="col-lg-12 col-md-12">
                                <div class="input-md form-full-width contact-field p-relative c-name mb-20">
                                    <label class="custom-authentication-label" for="password">
                                        <span><?php echo e(__('Password')); ?></span>
                                    </label>
                                    <input class="custom-authentication-input<?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" type="password" id="password" name="password" required />
                                    <?php echo Form::error('password', $errors); ?>

                                </div>
                            </div>
                        </div>

                        <div class="form-field-wrapper form-group">
                            <div class="col-lg-12 col-md-12">
                                <div class="input-md form-full-width contact-field p-relative c-name mb-20">
                                    <label class="custom-authentication-label" for="password_confirmation">
                                        <span><?php echo e(__('Confirm Password')); ?></span>
                                    </label>
                                    <input class="custom-authentication-input" type="password" id="password_confirmation" name="password_confirmation" required />
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 mt-15">
                            <div class="form-group mb-25">
                                <label>
                                    <span class="text-small">
                                        <?php echo e(__('Your personal data will be used to support your experience throughout this website, to manage access to your account, and for other purposes described in our privacy policy.')); ?>

                                    </span>
                                </label>
                            </div>
                            <div class="form-group mb-25">
                                <label class="cb-container">
                                    <input type="checkbox" name="agree_terms_and_policy" id="agree-terms-and-policy" value="1" <?php if(old('agree_terms_and_policy') == 1): ?> checked <?php endif; ?>>
                                    <span class="text-small"><?php echo e(__('I agree to terms & Policy.')); ?></span>
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                        </div>

                        <?php if(is_plugin_active('captcha')): ?> <?php if(Captcha::isEnabled() && HotelHelper::getSetting('enable_recaptcha_in_register_page', 0)): ?>
                            <div class="form-group mb-3">
                                <?php echo Captcha::display(); ?>

                            </div>
                        <?php endif; ?> <?php if(HotelHelper::getSetting('enable_math_captcha_in_register_page', 0)): ?>
                            <div class="form-group mb-3">
                                <?php echo app('math-captcha')->input(['class' => 'form-control', 'id' => 'math-group', 'placeholder' => app('math-captcha')->getMathLabelOnly()]); ?>

                            </div>
                        <?php endif; ?> <?php endif; ?>

                        <div class="form-group mb-3">
                            <div class="col-md-12 col-md-offset-4">
                                <button type="submit" class="submit btn btn-md btn-black">
                                    <?php echo e(__('Register')); ?>

                                </button>
                            </div>
                        </div>
                        <div class="text-center">
                            <?php echo apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Botble\Hotel\Models\Customer::class); ?>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/views/hotel/customers/register.blade.php ENDPATH**/ ?>