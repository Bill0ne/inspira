<?php if(is_plugin_active('blog')): ?>
    <section id="search-3" class="widget widget_search">
        <?php if($title = $config['title']): ?>
            <h2 class="widget-title"><?php echo e($title); ?></h2>
        <?php endif; ?>
        <form role="search" method="get" class="search-form custom-search-form" action="<?php echo e(route('public.search')); ?>">
            <label>
                <span class="screen-reader-text">Search for:</span>
                <input type="search" class="search-field" placeholder="<?php echo e(__('Search...')); ?>" value="<?php echo e(BaseHelper::stringify(request()->query('q'))); ?>" name="q" required/>
            </label>
            <button type="submit" class="btn btn-custom"><?php echo e(__('Search')); ?></button>
        </form>
    </section>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/////widgets/blog-search/templates/frontend.blade.php ENDPATH**/ ?>