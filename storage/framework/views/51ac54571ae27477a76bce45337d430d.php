<div class="bsingle__post mb-50">
    <div class="bsingle__post-thumb blog-active hover-zoomin wow fadeInUp animated">
        <?php if($image = $post->image): ?>
            <div class="slide-post">
                <a title="<?php echo e($post->name); ?>" class="blog-item-custom-truncate" href="<?php echo e($post->url); ?>">
                    <img src="<?php echo e(RvMedia::getImageUrl($image, 'medium')); ?>" alt="<?php echo e($post->name); ?>">
                </a>
            </div>
        <?php endif; ?>

    </div>
    <div class="bsingle__content">
        <div class="date-home">
            <?php echo e($post->created_at->translatedFormat('dS F Y')); ?>

        </div>
        <h2><a title="<?php echo e($post->name); ?>" class="blog-item-custom-truncate" href="<?php echo e($post->url); ?>"><?php echo e($post->name); ?></a></h2>

        <?php if($description = $post->description): ?>
            <p class="blog-item-custom-truncate" title="<?php echo e($description); ?>"><?php echo BaseHelper::clean($description); ?></p>
        <?php endif; ?>
        <div class="blog__btn">
            <a href="<?php echo e($post->url); ?>"><?php echo e(__('Read More')); ?></a>
        </div>
    </div>
</div>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/partials/blog/post/item.blade.php ENDPATH**/ ?>