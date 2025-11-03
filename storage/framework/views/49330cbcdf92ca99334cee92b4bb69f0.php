<?php (Theme::set('pageTitle', 'Kurse')); ?>

<section class="container courses-page mt-4 mb-5">
    <?php echo $__env->make(Theme::getThemeNamespace('partials.filters'), ['filterType' => 'courses'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="row">
        <div class="col-lg-12">
            
            <?php echo do_shortcode('[all-courses]'); ?>

        </div>
    </div>
</section>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/views/courses/courses.blade.php ENDPATH**/ ?>