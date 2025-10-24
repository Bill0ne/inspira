<nav class="navbar navbar-expand-lg bg-body-tertiary menu-mobile d-lg-none">
    <div class="collapse navbar-collapse" id="menu-mobile-nav" >
        <div class="menu">
            <div class="menu-title">
                <span><?php echo e(__('Menu')); ?></span>
            </div>
            <?php echo Menu::renderMenuLocation('main-menu', [
                'view' => 'menu-mobile',
                'options' => ['class' => 'navbar-nav mb-2 mb-lg-0 me-3 ms-3'],
            ]); ?>

            <?php if(is_plugin_active('language') && ($supportedLocales = Language::getSupportedLocales()) && count($supportedLocales) > 1): ?>
                <div class="menu-title mt-20">
                    <span><?php echo e(__('Languages')); ?></span>
                </div>
                <ul class="navbar-nav mb-2 mb-lg-0 me-3 ms-3">
                    <?php echo Theme::partial('language-switcher-mobile'); ?>

                </ul>
            <?php endif; ?>
            <div class="menu-title mt-20">
                <span><?php echo e(__('Currencies')); ?></span>
            </div>
            <ul class="navbar-nav mb-2 mb-lg-0 me-3 ms-3">
                <?php echo Theme::partial('currency-switcher-mobile'); ?>

            </ul>

            <?php if(is_plugin_active('hotel')): ?>
                <div class="menu-title mt-20">
                    <span><?php echo e(__('Account')); ?></span>
                </div>
                <ul class="navbar-nav mb-2 mb-lg-0 me-3 ms-3">
                    <li><a href="<?php echo e(route('customer.login')); ?>"><?php echo e(__('Login')); ?></a></li>
                    <li><a href="<?php echo e(route('customer.register')); ?>"><?php echo e(__('Register')); ?></a></li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/partials/menu-mobile-collapse.blade.php ENDPATH**/ ?>