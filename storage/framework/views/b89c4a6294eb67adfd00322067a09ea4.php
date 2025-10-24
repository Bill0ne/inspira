<?php ($fullWidth =  $fullWidth ?? false); ?>

<div class="header-top second-header d-none d-md-block">
    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['container' => ! $fullWidth, 'container-fluid' => $fullWidth]); ?>">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-6 d-none d-lg-block header-top-left">
                <div class="header-cta">
                    <ul>
                        <?php if($openingHours = theme_option('opening_hours')): ?>
                            <li class="opening_hours">
                                <i class="far fa-clock"></i>
                                <span><?php echo BaseHelper::clean($openingHours); ?></span>
                            </li>
                        <?php endif; ?>

                        <?php if($phoneNumber = theme_option('hotline')): ?>
                            <li>
                                <i class="far fa-mobile"></i>
                                <strong><a href="tel:<?php echo e($phoneNumber); ?>"><?php echo e($phoneNumber); ?></a></strong>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 d-none d-lg-block text-end header-top-end">
                <div class="header-social">
                    <?php echo Theme::partial('language-switcher'); ?>

                    <?php if(is_plugin_active('hotel')): ?>
                        <?php if(auth()->guard('customer')->check()): ?>
                            <a href="<?php echo e(route('customer.overview')); ?>">
                                <img src="<?php echo e(auth('customer')->user()->avatar_url); ?>" class="rounded-circle ms-3 text-white customer-avatar-header" title="<?php echo e(auth('customer')->user()->name); ?>" width="16" alt="<?php echo e(auth('customer')->user()->name); ?>">
                                <span class="customer-name text-white ms-1 customer-name-header"><?php echo e(auth('customer')->user()->name); ?></span>
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('customer.login')); ?>" class="ms-3">
                                <i class="fa fa-sign-in-alt"></i>
                                <span class="text-white customer-name-header ms-1"><?php echo e(__('Login')); ?></span>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if($socialLinks = json_decode(theme_option('social_links'))): ?>
    <span class="social-links">
        <?php $__currentLoopData = collect($socialLinks)->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php ($social = collect($social)->pluck('value', 'key')); ?>
            <a target="_blank" href="<?php echo e($social->get('url')); ?>" title="<?php echo e($social->get('name')); ?>">
                <i class="<?php echo e($social->get('social-icon')); ?>"></i>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </span>
<?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="language-switcher-mobile d-none">
    <?php echo Theme::partial('language-switcher-mobile'); ?>

</div>

<div class="currency-switcher-mobile d-none">
    <?php echo Theme::partial('currency-switcher-mobile'); ?>

</div>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/partials/header-top.blade.php ENDPATH**/ ?>