<?php (Theme::set('pageTitle', __('Rooms'))); ?>

<section class="container rooms-page mt-4 mb-5">
    <?php echo $__env->make(Theme::getThemeNamespace('partials.filters'), ['filterType' => 'rooms'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="row">
        <div class="col-lg-12">
            <?php echo do_shortcode('[all-rooms]'); ?>

        </div>
    </div>
</section>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/views/hotel/rooms.blade.php ENDPATH**/ ?>