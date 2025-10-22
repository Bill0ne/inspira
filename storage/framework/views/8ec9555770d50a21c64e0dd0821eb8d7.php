<?php $__env->startSection('content'); ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h1 class="text-center mb-20"><?php echo e(SeoHelper::getTitle()); ?></h1>
        </div>
        <div class="panel-body">
            <div class="section-content">
                <div class="table-responsive mb-20">
                    <table class="table table-striped custom-booking-table">
                        <thead class="text-center">
                        <tr>
                            <th><?php echo e(__('Room')); ?></th>
                            <th><?php echo e(__('Image')); ?></th>
                            <th><?php echo e(__('Amount')); ?></th>
                            <th><?php echo e(__('Booking Period')); ?></th>
                            <th><?php echo e(__('Status')); ?></th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody class="text-center">
                        <?php if(count($bookings) > 0): ?>
                            <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="align-middle">
                                    <?php if(($booking->room->room->exists) && ($room = $booking->room->room)): ?>
                                        <td>
                                            <a class="booking-information-link" href="<?php echo e($room->url); ?>" target="_blank">
                                                <?php echo e($room->name); ?>

                                            </a>
                                        </td>
                                        <td>
                                            <a href="<?php echo e($room->url); ?>" target="_blank">
                                                <img src="<?php echo e(RvMedia::getImageUrl($room->image, 'thumb', false, RvMedia::getDefaultImage())); ?>" alt="<?php echo e($booking->room->name); ?>" width="100">
                                            </a>
                                        </td>
                                    <?php else: ?>
                                        <td>
                                            <?php echo e($booking->room->name); ?>

                                        </td>
                                        <td>
                                            <img src="<?php echo e(RvMedia::getImageUrl($booking->room->room_image, 'thumb', false, RvMedia::getDefaultImage())); ?>" alt="<?php echo e($booking->room->name); ?>" width="100">
                                        </td>
                                    <?php endif; ?>
                                    <td><?php echo e(format_price($booking->room->price)); ?></td>
                                    <td><?php echo e($booking->room->booking_period); ?></td>
                                    <td><?php echo e($booking->status->label()); ?></td>
                                    <td>
                                        <a class="btn btn-primary btn-sm"
                                           href="<?php echo e(route('customer.bookings.show', $booking->transaction_id)); ?>"><?php echo e(__('View')); ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center"><?php echo e(__('No bookings!')); ?></td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php echo $bookings->links(Theme::getThemeNamespace('partials.pagination')); ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(HotelHelper::viewPath('customers.master'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/views/hotel/customers/bookings/list.blade.php ENDPATH**/ ?>