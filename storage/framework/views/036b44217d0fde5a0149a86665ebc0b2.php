<?php (Theme::set('pageTitle', $service->name)); ?>

<section>
    <div class="about-area5 about-p p-relative">
        <div class="container pt-120 pb-90">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-4 order-1">
                    <aside class="sidebar services-sidebar">
                        <div class="sidebar-widget categories">
                            <?php if($services->isNotEmpty()): ?>
                                <div class="widget-content">
                                    <h2 class="widget-title"> <?php echo e(__('Services')); ?> </h2>
                                    <ul class="services-categories">
                                        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $serviceItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => request()->url() === $serviceItem->url]); ?>"><a href="<?php echo e($serviceItem->url); ?>"><?php echo e($serviceItem->name); ?></a></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                       <?php echo dynamic_sidebar('service_sidebar'); ?>

                    </aside>
                </div>

                <div class="col-lg-8 col-md-12 col-sm-12 order-2">
                    <?php echo BaseHelper::clean($service->content); ?>

                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/views/hotel/service.blade.php ENDPATH**/ ?>