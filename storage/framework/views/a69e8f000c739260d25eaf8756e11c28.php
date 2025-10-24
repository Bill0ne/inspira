<section class="services-area pt-90 pb-90">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12">
                <div class="section-title center-align mb-50 text-center">
                    <?php if($subtitle = $shortcode->subtitle): ?>
                        <h5><?php echo BaseHelper::clean($subtitle); ?></h5>
                    <?php endif; ?>

                    <?php if($title = $shortcode->title): ?>
                        <h2><?php echo BaseHelper::clean($title); ?></h2>
                    <?php endif; ?>

                    <?php if($description = $shortcode->description): ?>
                        <p><?php echo BaseHelper::clean($description); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="row services-active">
            <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-xl-4 col-md-6">
                    <?php ($margin = true); ?>
                    <?php echo Theme::partial('rooms.item', compact('room', 'startDate', 'endDate', 'nights', 'adults', 'margin')); ?>

                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/partials/shortcodes/featured-rooms/index.blade.php ENDPATH**/ ?>