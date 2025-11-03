<table class="table table-bordered">
    <thead>
    <tr>
        <th>#</th>
        <th><?php echo e(__('Name')); ?></th>
        <th><?php echo e(__('E-mail')); ?></th>
        <th><?php echo e(__('Phone')); ?></th>
        <th>Zahlungsstatus</th>
        <th><?php echo e(__('Action')); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
            <td><?php echo e($index + 1); ?></td>
            <?php if($booking->customer && $booking->customer->id): ?>
                <td><?php echo e($booking->customer->first_name); ?> <?php echo e($booking->customer->last_name); ?></td>
            <?php elseif($booking->address): ?>
                <td><?php echo e($booking->address->first_name); ?> <?php echo e($booking->address->last_name); ?></td>
            <?php else: ?>
                <td><?php echo e(__('N/A')); ?></td>
            <?php endif; ?>
            <?php if($booking->customer && $booking->customer->email): ?>
                <td><?php echo e($booking->customer->email); ?></td>
            <?php elseif($booking->address && $booking->address->email): ?>
                <td><?php echo e($booking->address->email); ?></td>
            <?php else: ?>
                <td><?php echo e(__('N/A')); ?></td>
            <?php endif; ?>
            <?php if($booking->customer && $booking->customer->phone): ?>
                <td><?php echo e($booking->customer->phone); ?></td>
            <?php elseif($booking->address && $booking->address->phone): ?>
                <td><?php echo e($booking->address->phone); ?></td>
            <?php else: ?>
                <td><?php echo e(__('N/A')); ?></td>
            <?php endif; ?>
            <td><?php echo $booking->payment?->status->toHtml() ?? __('N/A'); ?></td>
            <td>
                <a href="<?php echo e(route('course-booking.edit', $booking->id)); ?>" class="btn btn-sm btn-primary">
                    <?php echo e(__('View')); ?>

                </a>
            </td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
            <td colspan="6" class="text-center"><?php echo e(__('No participants found.')); ?></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
<?php /**PATH D:\xampp\htdocs\inspira\platform/plugins/courses/resources/views/participants.blade.php ENDPATH**/ ?>