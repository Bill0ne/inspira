<section class="courses-area pt-20 pb-40">
    <h3 class="mb-20">
        <?php echo e(__(':count Kurse verfügbar', ['count' => $courses->total()])); ?>

    </h3>

    <?php if($courses->isNotEmpty()): ?>
        <div class="row">
            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-6 mb-4">
                    <?php echo Theme::partial('courses.item', compact('course')); ?>

                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php if($courses instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator): ?>
            <div class="text-center mt-30">
                <?php echo $courses->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')); ?>

            </div>
        <?php endif; ?>
    <?php else: ?>
        <p><?php echo e(__('Derzeit sind keine Kurse verfügbar')); ?></p>
    <?php endif; ?>
</section>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/partials/shortcodes/all-courses/index.blade.php ENDPATH**/ ?>