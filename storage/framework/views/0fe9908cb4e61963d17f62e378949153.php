<table class="table table-bordered">
    <thead>
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>E-mail</th>
        <th>Telefon</th>
        <th>Zahlungsstatus</th>
        <th>Aktion</th>
    </tr>
    </thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
            <td><?php echo e($index + 1); ?></td>
            <td><?php echo e($booking->customer->name ?? 'N/A'); ?></td>
            <td><?php echo e($booking->customer->email ?? 'N/A'); ?></td>
            <td><?php echo e($booking->customer->phone ?? 'N/A'); ?></td>
            <td><?php echo $booking->payment?->status->toHtml(); ?></td>
            <td>
                <a href="<?php echo e(route('course-booking.edit', $booking->id)); ?>" class="btn btn-sm btn-primary">
                    Sicht
                </a>
            </td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
            <td colspan="6" class="text-center">No participants found.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/plugins/courses/resources/views/participants.blade.php ENDPATH**/ ?>