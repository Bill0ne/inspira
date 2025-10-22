<?php
    Theme::asset()->usePath()->add('jquery-bar-rating-css', 'plugins/jquery-bar-rating/css-stars.css');
    Theme::asset()->container('footer')->usePath()->add('jquery-bar-rating-js', 'plugins/jquery-bar-rating/jquery.barrating.min.js');
    Theme::asset()->container('footer')->usePath()->add('review-js', 'js/review.js');
?>

<?php if(HotelHelper::isReviewEnabled()): ?>
    <?php
        $canReview = false;
        if ($isLoggedIn = auth('customer')->check()) {
            $hasBooked = auth('customer')->user()->hasBooked($model);
            $hasReviewed = auth('customer')->user()->hasReviewed($model);
            $canReview = $hasBooked && ! $hasReviewed;
        }
    ?>
    <div class="room-block-content shadow-block mt-50">
        <div>
            <div>
                <h3 class="text-xl"><?php echo e(__('Write a review')); ?></h3>
                <form action="<?php echo e(route('customer.ajax.review.store', $model->slug)); ?>" method="post" class="space-y-3 review-form">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="room_id" value="<?php echo e($model->id); ?>">
                    <div class="text-start mb-20">
                        <select name="star" id="select-star">
                            <?php $__currentLoopData = range(1, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($i); ?>" <?php if(old('score', 5) === $i): echo 'selected'; endif; ?>><?php echo e($i); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <textarea name="content" id="content" class="form-input custom-review-input mb-20" placeholder="<?php echo e(__('Enter your message')); ?>" <?php if(! $canReview): echo 'disabled'; endif; ?>><?php echo e(old('content')); ?></textarea>
                    </div>

                    <?php if(! $isLoggedIn): ?>
                        <p class="text-danger"><?php echo e(__('Please log in to write review!')); ?></p>
                    <?php else: ?>
                        <?php if(! $hasBooked): ?>
                            <p class="text-danger"><?php echo e(__('You need book this room to write a review!')); ?></p>
                        <?php elseif($hasReviewed): ?>
                            <p class="text-danger"><?php echo e(__('You have written a review for this room!')); ?></p>
                        <?php endif; ?>
                    <?php endif; ?>

                    <button type="submit" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['custom-submit-review-btn mb-20']); ?>" <?php if(! $canReview): echo 'disabled'; endif; ?>>
                        <?php echo e(__('Submit review')); ?>

                    </button>
                </form>
            </div>
            <div class="pt-8 mt-8 border-top">
                <?php if($model->reviews_count): ?>
                    <div class="d-flex justify-content-between mt-10 mb-20 reviews-block">
                        <h4 class="">
                            <span class="reviews-count"><?php echo e(__(':count Review(s)', ['count' => $model->approved_review_count])); ?></span>
                        </h4>
                        <div class="loading-spinner d-none"></div>

                        <?php echo $__env->make(Theme::getThemeNamespace('views.hotel.partials.review-star'), ['avgStar' => $model->reviews_avg_star, 'count' => $model->reviews_count], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                <?php endif; ?>
                <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['reviews-list mb-20', 'mt-10' => $model->approved_review_count]); ?>" data-url="<?php echo e(route('customer.ajax.review.index', $model->slug)); ?>?"></div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/views/hotel/partials/reviews.blade.php ENDPATH**/ ?>