<section class="hotel-service-area pt-40 pb-40 p-relative">
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

            <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-lg-4 col-md-6 services">
                    <div class="services-08-item service-item mb-30">
                        <?php if($image = $service->image): ?>
                            <?php
                                $imageHtml = RvMedia::image($image, $service->name, 'thumb');
                            ?>
                            <div class="services-icon2">
                                <?php echo e($imageHtml); ?>

                            </div>
                            <div class="services-08-thumb">
                                <?php echo e($imageHtml); ?>

                            </div>
                        <?php endif; ?>

                        <div class="services-08-content">
                            <h3><a href="<?php echo e($service->url); ?>"> <?php echo e($service->name); ?> </a></h3>

                            <div class="mb-3 h6 service-price">
                                <?php echo e(format_price($service->price) . '/' . $service->price_type->label()); ?>

                            </div>

                            <?php if($description = $service->description): ?>
                                <p title="<?php echo e($description); ?>"><?php echo BaseHelper::clean(Str::limit($description, 80)); ?></p>
                            <?php endif; ?>

                            <a href="<?php echo e($service->url); ?>"><?php echo e(__('Read More')); ?> <i class="fal fa-long-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/partials/shortcodes/hotel-services/index.blade.php ENDPATH**/ ?>