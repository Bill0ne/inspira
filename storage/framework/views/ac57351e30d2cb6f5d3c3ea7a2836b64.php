<?php
    Theme::set('pageTitle', __('Blog Search'));
    $enabledSidebar = theme_option('blog_sidebar_enabled', 1)
?>

<section class="inner-blog pt-80">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <?php echo $__env->make(Theme::getThemeNamespace('views.loop'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

            <div class="col-sm-12 col-md-12 col-lg-4">
                <aside class="sidebar-widget">
                    <?php echo dynamic_sidebar('blog_sidebar'); ?>

                </aside>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/views/search.blade.php ENDPATH**/ ?>