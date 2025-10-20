<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0"><?php echo e(trans('plugins/hotel::booking.customer_information')); ?></h5>
    </div>
    <div class="card-body">
        <div><strong><?php echo e(trans('plugins/hotel::booking.name')); ?>:</strong> <?php echo e($customer->first_name); ?> <?php echo e($customer->last_name); ?></div>
        <div><strong>Email:</strong> <?php echo e($customer->email); ?></div>
        <div><strong><?php echo e(trans('plugins/hotel::booking.phone')); ?>:</strong> <?php echo e($customer->phone ?? 'N/A'); ?></div>
        <div><strong><?php echo e(trans('plugins/hotel::booking.address')); ?>:</strong> <?php echo e($customer->address ?? 'N/A'); ?></div>
    </div>
</div>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/plugins/hotel/resources/views/customer-info.blade.php ENDPATH**/ ?>