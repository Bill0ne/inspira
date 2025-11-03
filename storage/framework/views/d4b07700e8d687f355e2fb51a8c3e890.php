<section class="services-area pt-20 pb-40">
    <?php if($rooms->isNotEmpty()): ?>
        <div class="row g-4">
            <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <?php echo Theme::partial('rooms.item', compact('room', 'startDate', 'endDate', 'nights', 'adults')); ?>

                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php if($rooms instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator): ?>
            <div class="text-center mt-30">
                <?php echo $rooms->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')); ?>

            </div>
        <?php endif; ?>
    <?php else: ?>
        <p><?php echo e(__('Derzeit sind keine Räume verfügbar')); ?></p>
    <?php endif; ?>
</section>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/partials/shortcodes/all-rooms/index.blade.php ENDPATH**/ ?>