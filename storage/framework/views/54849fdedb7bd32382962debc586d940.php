<section class="pt-100 pb-90 p-relative">
    <?php if($bgImage = $shortcode->background_image): ?>
        <div class="animations-01">
            <img src="<?php echo e(RvMedia::getImageUrl($bgImage)); ?>" alt="<?php echo e(__('Background image')); ?>">
        </div>
    <?php endif; ?>
        <div class="container">
            <div class="row align-items-center">
                <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="services-08-item mb-30">
                            <?php if($image = $service->image): ?>
                                <div class="services-icon2">
                                    <img src="<?php echo e(RvMedia::getImageUrl($image)); ?>" alt="<?php echo e($service->name); ?>">
                                </div>
                                <div class="services-08-thumb">
                                    <img src="<?php echo e(RvMedia::getImageUrl($image)); ?>" alt="<?php echo e($service->name); ?>">
                                </div>
                            <?php endif; ?>
                            <div class="services-08-content">
                                <h3><a href="<?php echo e($service->url); ?>"><?php echo e($service->name); ?></a></h3>
                                <p><?php echo BaseHelper::clean(Str::limit($service->description, 120)); ?></p>
                                <a href="<?php echo e($service->url); ?>"><?php echo e(__('Read More')); ?><i class="fal fa-long-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
</section>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/partials/shortcodes/service-list/index.blade.php ENDPATH**/ ?>