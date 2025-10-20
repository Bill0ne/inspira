<?php
    SeoHelper::setTitle(__('404 - Not found'));
    Theme::fireEventGlobalAssets();
    $image = theme_option('404_page_image') ?
        RvMedia::getImageUrl(theme_option('404_page_image')) :
        Theme::asset()->url('/images/404.png');
?>


<?php $__env->startSection('main'); ?>
    <header class="header-area header-three">
        <?php if(theme_option('header_top_enabled', true)): ?>
            <?php echo Theme::partial('header-top', ['fullWidth' => true]); ?>

        <?php endif; ?>

        <?php echo Theme::partial('header', ['fullWidth' => false]); ?>

    </header>
    <section class="error-page">
        <div class="pt-160 pb-60 text-center d-sm-flex d-block container center align-items-center">
            <div>
                <img src="<?php echo e(RvMedia::getImageUrl($image)); ?>" alt="<?php echo e(__('404 Not Found')); ?>"/>
            </div>
            <div class="ms-0 ms-sm-5">
                <h2><?php echo e(__('Oops, nothing to see here')); ?>:</h2>
                <ul>
                    <li><?php echo e(__("Unfortunately, we couldn't find what you were looking for or the page no longer exists.")); ?></li>
                </ul>
                <br>
                <a href="<?php echo e(route('public.index')); ?>">
                    <i class="fal fa-long-arrow-left me-1"></i>
                    <?php echo e(__('Back to Homepage')); ?>

                </a>
            </div>
        </div>
    </section>

    <?php echo Theme::partial('footer'); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make(Theme::getThemeNamespace('layouts.base'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/views/404.blade.php ENDPATH**/ ?>