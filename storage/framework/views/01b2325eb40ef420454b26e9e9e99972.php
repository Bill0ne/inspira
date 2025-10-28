<?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="d-flex border-bottom pt-10 pb-10 review-item-block">
        <div class="img-block">
            <img class="review-avatar-img" src="<?php echo e($review->author->avatar_url); ?>" alt="<?php echo e($review->author->name); ?>"/>
        </div>

        <div class="ms-5">
            <div class="d-flex items-center">
                <div class="rating-wrap">
                    <div class="rating">
                        <div class="review-rate" style="width: <?php echo e($review->star * 20); ?>%"></div>
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <span class="reviewer-name">
                    <?php echo e($review->author->name); ?>

                </span>
                <span class="review-time"><?php echo e($review->created_at->diffForHumans()); ?></span>
            </div>
            <p class="review-content"><?php echo e($review->content); ?></p>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<div class="pb-10"></div>

<?php echo e($reviews->onEachSide(1)->links(Theme::getThemeNamespace('partials.pagination'))); ?>

<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/views/hotel/partials/reviews-list.blade.php ENDPATH**/ ?>