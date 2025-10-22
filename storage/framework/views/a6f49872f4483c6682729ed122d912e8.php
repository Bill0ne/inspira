<?php $__env->startSection('content'); ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h1 class="text-center mb-20"><?php echo e(SeoHelper::getTitle()); ?></h1>
        </div>

        <div class="panel-body">
            <div class="section-content">
                <div class="table-responsive mb-20">
                    <table class="table table-striped custom-review-table">
                        <thead class="text-center">
                            <tr>
                                <th style="width: 15%"><?php echo e(__('Room')); ?></th>
                                <th style="width: 15%"><?php echo e(__('Image')); ?></th>
                                <th><?php echo e(__('Content')); ?></th>
                            </tr>
                        </thead>

                        <tbody class="text-center">
                        <?php if(count($reviews) > 0): ?>
                            <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php ($room = $review->room); ?>
                                <tr class="align-middle">
                                        <td>
                                            <a class="review-information-link" href="<?php echo e($room->url); ?>" target="_blank">
                                                <?php echo e($room->name); ?>

                                            </a>
                                        </td>
                                        <td>
                                            <a href="<?php echo e($room->url); ?>" target="_blank">
                                                <img src="<?php echo e(RvMedia::getImageUrl($room->image, 'thumb', false, RvMedia::getDefaultImage())); ?>" alt="<?php echo e($review->room->name); ?>" width="120">
                                            </a>
                                        </td>
                                    <td><?php echo e($review->content); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center"><?php echo e(__('No reviews!')); ?></td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php echo $reviews->links(HotelHelper::viewPath('partials.pagination')); ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(HotelHelper::viewPath('customers.master'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/views/hotel/customers/reviews.blade.php ENDPATH**/ ?>