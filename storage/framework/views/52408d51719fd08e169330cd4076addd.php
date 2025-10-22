<?php
    $perItem = theme_option('number_of_post_per_row', 2)
?>

<div class="row">
    <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'col-12' => $perItem == 1,
            'col-lg-6' => $perItem == 2,
            'col-lg-4' => $perItem == 3,
        ]); ?>">
            <?php echo Theme::partial('blog.post.item', compact('post')); ?>

        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php if($posts instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator): ?>
        <div class="text-center mt-30">
            <?php echo $posts->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')); ?>

        </div>
    <?php endif; ?>
</div>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/partials/blog/posts.blade.php ENDPATH**/ ?>