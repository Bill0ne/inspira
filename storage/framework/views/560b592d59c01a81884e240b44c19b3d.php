<?php if(is_plugin_active('blog')): ?>
    <?php
        $limit = (int) Arr::get($config, 'limit', 10);
        $type = Arr::get($config, 'type');

        if ($limit > 0) {
            $tags = get_popular_tags($limit);
        } else {
            $tags = get_all_tags();
        }
    ?>

    <?php if($tags->count()): ?>
    <section id="tag_cloud-1" class="widget widget_tag_cloud">
        <?php if($title = $config['title']): ?>
            <h2 class="widget-title"><?php echo e($title); ?></h2>
        <?php endif; ?>
        <div class="tagcloud">
            <?php $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($tag->url); ?>" class="tag-cloud-link tag-link-28 tag-link-position-1 custom-blog-tag-sidebar" style="font-size: 8pt;" aria-label="<?php echo e($tag->name); ?>">
                    <?php echo e($tag->name); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
    <?php endif; ?>
<?php endif; ?>

<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/////widgets/blog-tags/templates/frontend.blade.php ENDPATH**/ ?>