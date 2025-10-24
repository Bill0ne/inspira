<?php if (isset($component)) { $__componentOriginalc107e2f90dff5eb05519f33918d2c807 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc107e2f90dff5eb05519f33918d2c807 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::card.index','data' => ['class' => 'report-chart-content','id' => 'report-chart']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'report-chart-content','id' => 'report-chart']); ?>
    <?php if (isset($component)) { $__componentOriginalf7ec4b8ef3fc6db54b9665bd653222c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf7ec4b8ef3fc6db54b9665bd653222c4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::card.header.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::card.header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <h4 class="card-title"><?php echo e(trans('plugins/hotel::booking-reports.booking_reports')); ?></h4>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf7ec4b8ef3fc6db54b9665bd653222c4)): ?>
<?php $attributes = $__attributesOriginalf7ec4b8ef3fc6db54b9665bd653222c4; ?>
<?php unset($__attributesOriginalf7ec4b8ef3fc6db54b9665bd653222c4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf7ec4b8ef3fc6db54b9665bd653222c4)): ?>
<?php $component = $__componentOriginalf7ec4b8ef3fc6db54b9665bd653222c4; ?>
<?php unset($__componentOriginalf7ec4b8ef3fc6db54b9665bd653222c4); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal4fdb92edf089f19cd17d37829580c9a6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4fdb92edf089f19cd17d37829580c9a6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::card.body.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::card.body'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <div class="row">
            <div class="col-md-8 mb-2">
                <div id="sales-report-chart"></div>
                <?php if($earningBookings = $bookingsReport['earningBookings']): ?>
                    <div class="row">
                        <div class="col-12">
                            <ul class="list-unstyled mb-0">
                                <?php $__currentLoopData = $earningBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $earningBooking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <?php if (isset($component)) { $__componentOriginal73995948b3bd877b76251b40caf28170 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal73995948b3bd877b76251b40caf28170 = $attributes; } ?>
<?php $component = Botble\Icon\View\Components\Icon::resolve(['name' => 'ti ti-circle-filled'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Botble\Icon\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mb-0 me-1','style' => 'color: '.e($earningBooking['color']).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal73995948b3bd877b76251b40caf28170)): ?>
<?php $attributes = $__attributesOriginal73995948b3bd877b76251b40caf28170; ?>
<?php unset($__attributesOriginal73995948b3bd877b76251b40caf28170); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal73995948b3bd877b76251b40caf28170)): ?>
<?php $component = $__componentOriginal73995948b3bd877b76251b40caf28170; ?>
<?php unset($__componentOriginal73995948b3bd877b76251b40caf28170); ?>
<?php endif; ?>
                                        <?php echo e($earningBooking['text']); ?>

                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <?php if(collect($count['revenues'])->isNotEmpty()): ?>
                    <div class="rp-card-chart position-relative mb-3">
                        <div id="revenue-chart"></div>
                        <div class="rp-card-information">
                            <?php if (isset($component)) { $__componentOriginal73995948b3bd877b76251b40caf28170 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal73995948b3bd877b76251b40caf28170 = $attributes; } ?>
<?php $component = Botble\Icon\View\Components\Icon::resolve(['name' => 'ti ti-wallet'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Botble\Icon\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal73995948b3bd877b76251b40caf28170)): ?>
<?php $attributes = $__attributesOriginal73995948b3bd877b76251b40caf28170; ?>
<?php unset($__attributesOriginal73995948b3bd877b76251b40caf28170); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal73995948b3bd877b76251b40caf28170)): ?>
<?php $component = $__componentOriginal73995948b3bd877b76251b40caf28170; ?>
<?php unset($__componentOriginal73995948b3bd877b76251b40caf28170); ?>
<?php endif; ?>
                            <?php $__currentLoopData = collect($count['revenues'])->where('status'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <strong><?php echo e(format_price($item['value'])); ?></strong>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <small><?php echo e(trans('plugins/hotel::booking-reports.total_earnings')); ?></small>
                        </div>
                    </div>
                    <div class="rp-card-status text-center">
                        <?php $__currentLoopData = $count['revenues']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <p>
                                <?php if (isset($component)) { $__componentOriginal73995948b3bd877b76251b40caf28170 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal73995948b3bd877b76251b40caf28170 = $attributes; } ?>
<?php $component = Botble\Icon\View\Components\Icon::resolve(['name' => 'ti ti-circle-filled','size' => 'sm'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Botble\Icon\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mb-0 me-1','style' => 'color: '.e(Arr::get($item, 'color')).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal73995948b3bd877b76251b40caf28170)): ?>
<?php $attributes = $__attributesOriginal73995948b3bd877b76251b40caf28170; ?>
<?php unset($__attributesOriginal73995948b3bd877b76251b40caf28170); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal73995948b3bd877b76251b40caf28170)): ?>
<?php $component = $__componentOriginal73995948b3bd877b76251b40caf28170; ?>
<?php unset($__componentOriginal73995948b3bd877b76251b40caf28170); ?>
<?php endif; ?>
                                <strong><?php echo e(format_price($item['value'])); ?></strong>
                                <span class="ms-1"><?php echo e($item['label']); ?></span>
                            </p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div>
                        <?php echo $__env->make('core/dashboard::partials.no-data', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4fdb92edf089f19cd17d37829580c9a6)): ?>
<?php $attributes = $__attributesOriginal4fdb92edf089f19cd17d37829580c9a6; ?>
<?php unset($__attributesOriginal4fdb92edf089f19cd17d37829580c9a6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4fdb92edf089f19cd17d37829580c9a6)): ?>
<?php $component = $__componentOriginal4fdb92edf089f19cd17d37829580c9a6; ?>
<?php unset($__componentOriginal4fdb92edf089f19cd17d37829580c9a6); ?>
<?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc107e2f90dff5eb05519f33918d2c807)): ?>
<?php $attributes = $__attributesOriginalc107e2f90dff5eb05519f33918d2c807; ?>
<?php unset($__attributesOriginalc107e2f90dff5eb05519f33918d2c807); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc107e2f90dff5eb05519f33918d2c807)): ?>
<?php $component = $__componentOriginalc107e2f90dff5eb05519f33918d2c807; ?>
<?php unset($__componentOriginalc107e2f90dff5eb05519f33918d2c807); ?>
<?php endif; ?>

<?php if(request()->ajax()): ?>
    <?php echo $__env->make('plugins/hotel::reports.widgets.chart-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php else: ?>
    <?php $__env->startPush('footer'); ?>
        <?php echo $__env->make('plugins/hotel::reports.widgets.chart-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\inspira\platform/plugins/hotel/resources/views/reports/widgets/revenues.blade.php ENDPATH**/ ?>