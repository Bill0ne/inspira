<?php
    Theme::set('pageTitle',  __('Login'));
?>

<section class="about-area about-p pt-120 pb-120 p-relative fix">
    <div class="container">
        <div class="row flex-row-reverse justify-content-center align-items-center">
            <?php if($backgroundImage = theme_option('authentication_login_background_image')): ?>
                <div class="col-lg-6 col-md-6">
                    <div class="booking-img">
                        <img src="<?php echo e(RvMedia::getImageURL($backgroundImage)); ?>" alt="<?php echo e(__('Login')); ?>" />
                    </div>
                </div>
            <?php endif; ?>

            <div class="col-xxl-5 col-xl-12 col-lg-12">
                <div class="about-content s-about-content wow fadeInRight animated" data-animation="fadeInRight" data-delay=".4s" style="visibility: visible; animation-name: fadeInRight;">
                    <div class="about-title second-title pb-25">
                        <h1><?php echo e(__('Welcome back')); ?></h1>
                    </div>
                    <div class="form-border-box">
                        <form method="POST" action="<?php echo e(route('customer.login.post')); ?>">
                            <?php echo csrf_field(); ?>
                            <h2 class="normal pb-25"><span><?php echo e(__('Login')); ?></span></h2>
                            <?php if(isset($errors) && $errors->has('confirmation')): ?>
                                <div class="alert alert-danger">
                                    <span><?php echo $errors->first('confirmation'); ?></span>
                                </div>
                                <br />
                            <?php endif; ?>

                            <div class="form-field-wrapper form-group">
                                <div class="col-lg-12 col-md-12">
                                    <div class="input-md form-full-width contact-field p-relative c-name mb-20">
                                        <label class="custom-authentication-label" for="email">
                                            <span><?php echo e(__('Email')); ?></span>
                                        </label>
                                        <input class="custom-authentication-input <?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" type="email" id="email" name="email" placeholder="example@gmail.com" required value="<?php echo e(BaseHelper::stringify(old('email'))); ?>" />
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
                                        <input class="custom-authentication-input <?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" type="password" id="password" name="password" required />
                                        <?php echo Form::error('password', $errors); ?>

                                    </div>
                                </div>
                            </div>

                            <div class="d-flex">
                                <div class="col-lg-6 col-6 mt-15">
                                    <div class="form-group mb-25">
                                        <label class="cb-container">
                                            <input type="checkbox" value="1" name="remember" id="remember-check" <?php if(old('remember', 1)): ?> checked <?php endif; ?>>
                                            <span class="text-small"><?php echo e(__('Remember me')); ?></span>
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-6 mt-15">
                                    <div class="form-group mb-25 text-end">
                                        <a class="font-xs color-grey-500" href="<?php echo e(route('customer.password.request')); ?>">
                                            <?php echo e(__('Forgot password?')); ?>

                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="form-field-wrapper">
                                <button type="submit" class="submit btn btn-md btn-black">
                                    <?php echo e(__('Login')); ?>

                                </button>
                            </div>
                            <?php echo apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Botble\Hotel\Models\Customer::class); ?>

                        </form>
                    </div>
                </div>
                <div class="col-lg-12 mt-20">
                    <span class="color-grey-500 d-inline-block align-middle font-sm">
                        <?php echo e(__('Don’t have an account?')); ?>

                    </span>
                    <a class="d-inline-block align-middle color-success ms-1 custom-register-label" href="<?php echo e(route('customer.register')); ?>"><?php echo e(__('Sign up now')); ?></a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/views/hotel/customers/login.blade.php ENDPATH**/ ?>