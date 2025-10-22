

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
                            <th><?php echo e(__('Kurse')); ?></th>
                            <th><?php echo e(__('Dozenten')); ?></th>
                            <th><?php echo e(__('Kategorien')); ?></th>
                            <th><?php echo e(__('Preis')); ?></th>
                            <th><?php echo e(__('Buchungsdatum')); ?></th>
                            <th><?php echo e(__('Startdatum der Sitzung')); ?></th>
                            <th><?php echo e(__('Enddatum der Sitzung')); ?></th>
                            <th><?php echo e(trans('core/base::forms.status')); ?></th>
                        </tr>
                        </thead>
                        <tbody class="text-center">
                        <?php if($courseBookings->count() > 0): ?>
                            <?php $__currentLoopData = $courseBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo e($booking->course->url ?? '#'); ?>" target="_blank">
                                            <?php echo e($booking->course->name ?? '-'); ?>

                                        </a>
                                    </td>
                                    <td><?php echo e($booking->course->instructor->name ?? '-'); ?></td>
                                    <td><?php echo e($booking->course->category->name ?? '-'); ?></td>
                                    <td><?php echo e(format_price($booking->amount)); ?></td>
                                    <td><?php echo e($booking->created_at->format('d M Y')); ?></td>
                                    <td><?php echo e($booking->session->start_date?->format('d M Y H:i') ?? '-'); ?></td>
                                    <td><?php echo e($booking->session->end_date?->format('d M Y H:i') ?? '-'); ?></td>
                                    <td><?php echo $booking->status->toHtml(); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center"><?php echo e(__('No course bookings!')); ?></td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php echo $courseBookings->links(Theme::getThemeNamespace('partials.pagination')); ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(HotelHelper::viewPath('customers.master'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/views/courses/bookings/list.blade.php ENDPATH**/ ?>