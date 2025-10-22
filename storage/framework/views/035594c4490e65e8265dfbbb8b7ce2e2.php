<section class="testimonial-area pt-90 pb-90 p-relative fix"
         <?php if($bgImage = $shortcode->background_image): ?> style="background-image: url('<?php echo e(RvMedia::getImageUrl($bgImage)); ?>'); background-size: cover;" <?php endif; ?>
>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
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

            <?php if($testimonials->isNotEmpty()): ?>
                <div class="col-lg-12">
                    <div class="testimonial-active">
                        <?php $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testimonial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="single-testimonial">
                                <div class="testi-author">
                                    <img src="<?php echo e(RvMedia::getImageUrl($testimonial->image)); ?>" alt="<?php echo e($testimonial->name); ?>">
                                    <div class="ta-info w-100">
                                        <h6><?php echo e($testimonial->name); ?></h6>
                                    </div>
                                </div>
                                <div class="review-icon">
                                    <img src="<?php echo e(Theme::asset()->url('/images/testimonials/review-icon.png')); ?>" alt="<?php echo e(__('Icon reviews')); ?>">
                                </div>

                                <p><?php echo BaseHelper::clean($testimonial->content); ?></p>

                                <div class="qt-img">
                                    <img src="<?php echo e(Theme::asset()->url('/images/testimonials/qt-icon.png')); ?>" alt="<?php echo e(__('Icon')); ?>">
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/partials/shortcodes/testimonials/index.blade.php ENDPATH**/ ?>