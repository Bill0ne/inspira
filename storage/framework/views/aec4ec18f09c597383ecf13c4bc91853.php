<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <?php echo apply_filters(DASHBOARD_FILTER_ADMIN_NOTIFICATIONS, null); ?>

        </div>

        <div class="col-12">
            <div class="row row-cards">
                <?php $__currentLoopData = $statWidgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $widget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $widget; ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <div class="mb-3 col-12">
        <?php echo apply_filters(DASHBOARD_FILTER_TOP_BLOCKS, null); ?>

    </div>

    <div class="col-12">
        <div
            id="list_widgets"
            class="row row-cards"
            data-bb-toggle="widgets-list"
            data-url="<?php echo e(route('dashboard.update_widget_order')); ?>"
        >
            <?php $__currentLoopData = $userWidgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $widget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $widget; ?>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer'); ?>
    <?php echo $__env->make('core/dashboard::partials.modals', compact('widgets'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make(BaseHelper::getAdminMasterLayoutTemplate(), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\inspira\platform/core/dashboard/resources/views/list.blade.php ENDPATH**/ ?>