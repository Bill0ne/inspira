<?php if(is_plugin_active('blog')): ?>
    <?php
        $limit = (int) Arr::get($config, 'limit');
        $posts = match (Arr::get($config, 'type')) {
            'recent' => get_recent_posts($limit),
            default => get_popular_posts($limit),
        };
    ?>

    <section id="recent-posts-4" class="custom-blog-post-sidebar">
        <?php if($title = $config['title']): ?>
            <h2 class="widget-title"><?php echo e($title); ?></h2>
        <?php endif; ?>
        <ul>
            <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                    <div class="custom-blog-post-name">
                        <a href="<?php echo e($post->url); ?>"><?php echo e($post->name); ?></a>
                    </div>
                    <div>
                        <small><?php echo e($post->created_at->translatedFormat('M d, Y')); ?></small>
                    </div>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </section>
<?php endif; ?>


<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/////widgets/blog-posts/templates/frontend.blade.php ENDPATH**/ ?>