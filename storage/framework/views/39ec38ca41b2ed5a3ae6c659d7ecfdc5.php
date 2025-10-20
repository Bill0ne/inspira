<?php $__env->startSection('main'); ?>

    <header class="header-area header-three">
        <?php if(theme_option('header_top_enabled', true)): ?>
            <?php echo Theme::partial('header-top'); ?>

        <?php endif; ?>

        <?php echo Theme::partial('header'); ?>

    </header>

    <?php if(Theme::get('breadcrumb', true)): ?>
    <?php echo Theme::partial('breadcrumbs'); ?>

    <?php endif; ?>

    <section class="inner-blog pt-80">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <?php echo Theme::content(); ?>

                </div>

                <div class="col-sm-12 col-md-12 col-lg-4">
                    <aside class="sidebar-widget">
                        <?php echo dynamic_sidebar('blog_sidebar'); ?>

                    </aside>
                </div>
            </div>
        </div>
    </section>


    <?php echo Theme::partial('footer'); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make(Theme::getThemeNamespace('layouts.base'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/layouts/blog-sidebar.blade.php ENDPATH**/ ?>