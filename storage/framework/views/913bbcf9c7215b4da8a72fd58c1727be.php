<?php ($bgColor = $shortcode->background_color ?: '#f7f5f1'); ?>

<section id="service-details2" class="pt-90 pb-90 p-relative" style="background-color: <?php echo e($bgColor); ?>;">
    <?php if($bgImage = $shortcode->background_image): ?>
        <div class="animations-01">
            <img src="<?php echo e(RvMedia::getImageUrl($bgImage)); ?>" alt="<?php echo e(__('Background image')); ?>">
        </div>
    <?php endif; ?>
    <div class="container">
        <div class="row align-items-center">
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
            <?php $__currentLoopData = $amenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-lg-4 col-md-6">
                    <div class="services-08-item mb-30">
                        <?php if($image = $amenity->getMetaData('icon_image', true)): ?>
                            <div class="services-icon2">
                                <img src="<?php echo e(RvMedia::getImageUrl($image)); ?>" alt="<?php echo e($amenity->name); ?>">
                            </div>
                            <div class="services-08-thumb">
                                <img src="<?php echo e(RvMedia::getImageUrl($image)); ?>" alt="<?php echo e($amenity->name); ?>">
                            </div>
                        <?php endif; ?>

                        <div class="services-08-content">
                            <h3><?php echo e($amenity->name); ?></h3>

                            <?php if($description = $amenity->getMetaData('description', true)): ?>
                                <p title="<?php echo e($description); ?>"><?php echo BaseHelper::clean(Str::limit($description, 80)); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/partials/shortcodes/featured-amenities/index.blade.php ENDPATH**/ ?>