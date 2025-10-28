<section class="services-area pt-20 pb-40">
    <h3 class="mb-20"><?php echo e(__(':count rooms available', ['count' => $rooms->total()])); ?></h3>

    <?php if($rooms->isNotEmpty()): ?>
        <div class="row">
            <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-6">
                    <?php echo Theme::partial('rooms.item', compact('room', 'startDate', 'endDate', 'nights', 'adults')); ?>

                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($rooms instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator): ?>
            <div class="text-center mt-30">
                <?php echo $rooms->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')); ?>

            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/partials/shortcodes/all-rooms/index.blade.php ENDPATH**/ ?>