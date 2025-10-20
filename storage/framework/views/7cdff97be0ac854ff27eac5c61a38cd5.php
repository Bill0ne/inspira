<?php if(is_plugin_active('blog')): ?>
    <?php
        $limit = (int) Arr::get($config, 'limit', 10);
        $type = Arr::get($config, 'type');

        if ($limit > 0) {
            $categories = get_popular_categories($limit);
        } else {
            $categories = get_all_categories();
        }
    ?>

    <?php if($categories->count()): ?>
        <section id="categories-1" class="widget widget_categories">
            <?php if($title = $config['title']): ?>
                <h2 class="widget-title"><?php echo e($title); ?></h2>
            <?php endif; ?>
            <ul>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="cat-item cat-item-16">
                        <a href="<?php echo e($category->url); ?>"><?php echo e($category->name); ?></a>
                        <span class="float-end"><?php echo e(number_format($category->posts_count)); ?></span>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </section>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/////widgets/blog-categories/templates/frontend.blade.php ENDPATH**/ ?>