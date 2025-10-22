<?php if(count($customers) === 0): ?>
    <div class="dropdown-item"><?php echo e(trans('plugins/hotel::booking.no_customers_found')); ?></div>
<?php else: ?>
    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="javascript:void(0);" class="dropdown-item customer-item" data-id="<?php echo e($customer->id); ?>">
            <div><strong><?php echo e($customer->first_name); ?> <?php echo e($customer->last_name); ?></strong></div>
            <div><?php echo e($customer->email); ?></div>
            <div><?php echo e($customer->phone ?? ''); ?></div>
        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/plugins/hotel/resources/views/customer-search-results.blade.php ENDPATH**/ ?>