<section id="blog" class="blog-area p-relative fix pt-90 pb-90">
    <?php if($bgImage = $shortcode->bg_image): ?>
        <div class="animations-02">
            <img src="<?php echo e(RvMedia::getImageUrl($bgImage)); ?>" alt="<?php echo e(__('Background image')); ?>">
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <div class="section-title center-align mb-50 text-center wow fadeInDown animated" data-animation="fadeInDown" data-delay=".4s">
                    <?php if($subtitle = $shortcode->subtitle): ?>
                        <h5><?php echo BaseHelper::clean($subtitle); ?></h5>
                    <?php endif; ?>

                    <?php if($title = $shortcode->title): ?>
                         <h2><?php echo BaseHelper::clean($title); ?></h2>
                    <?php endif; ?>

                    <?php if($description = $shortcode->description): ?>
                        <p><?php echo BaseHelper::clean($description); ?></p>
                    <?php endif; ?>
                </div>

            </div>
        </div>
        <?php if($posts->isNotEmpty()): ?>
            <div class="row">
                <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-lg-4 col-md-6">
                        <?php echo Theme::partial('blog.post.item', compact('post')); ?>

                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/partials/shortcodes/news/index.blade.php ENDPATH**/ ?>