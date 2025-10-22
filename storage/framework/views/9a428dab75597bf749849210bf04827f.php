<?php
    Theme::set('pageTitle', SeoHelper::getTitle());
?>

<section class="about-area about-p pt-60 pb-60 p-relative fix">
    <div class="container">
        <div class="row flex-row-reverse justify-content-center align-items-center">
            <?php if($backgroundImage = theme_option('authentication_forgot_password_background_image')): ?>
                <div class="col-lg-6 col-md-6">
                    <div class="booking-img">
                        <img src="<?php echo e(RvMedia::getImageURL($backgroundImage)); ?>" alt="<?php echo e(__('Forgot password')); ?>" />
                    </div>
                </div>
            <?php endif; ?>
            <div class="col-md-6 col-lg-6">
                <h1><?php echo e(__('Forgot password')); ?></h1>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?php if(session('status')): ?>
                            <div class="alert alert-success">
                                <?php echo e(session('status')); ?>

                            </div>
                        <?php endif; ?>
                        <p><?php echo e(__('Enter the email address associated with your account and we’ll send you a link to reset your password')); ?></p>

                        <form class="form-horizontal" role="form" method="POST" action="<?php echo e(route('customer.password.email')); ?>">
                            <?php echo csrf_field(); ?>
                            <div class="form-field-wrapper form-group">
                                <div class="col-lg-12 col-md-12 mb-20">
                                    <div class="input-md form-full-width contact-field p-relative c-name <?php if($errors->has('email')): ?> is-invalid <?php endif; ?>">
                                        <label class="custom-authentication-label" for="email">
                                            <span><?php echo e(__('E-Mail Address')); ?></span>
                                        </label>
                                        <input class="custom-authentication-input" type="email" id="email" name="email" placeholder="<?php echo e(__('Email')); ?>" required />
                                    </div>

                                    <?php echo Form::error('email', $errors); ?>

                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <div class="col-md-6 col-md-offset-4 mt-20">
                                    <button type="submit" class="btn btn-primary">
                                        <?php echo e(__('Send Password Reset Link')); ?>

                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/views/hotel/customers/passwords/email.blade.php ENDPATH**/ ?>