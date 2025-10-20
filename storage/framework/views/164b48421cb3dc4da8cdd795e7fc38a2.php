<?php if(is_plugin_active('blog')): ?>
    <section id="custom_html-5" class="widget_text widget widget_custom_html">
        <h2 class="widget-title">Follow Us</h2>
        <div class="textwidget custom-html-widget">
            <div class="widget-social">
                <?php $__currentLoopData = range(1, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(Arr::get($config, "link_$i") && Arr::get($config, "icon_$i")): ?>
                        <a target="_blank" href="<?php echo e(Arr::get($config, "link_$i")); ?>">
                            <i class="<?php echo e(Arr::get($config, "icon_$i")); ?>"></i>
                        </a>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/////widgets/blog-socials/templates/frontend.blade.php ENDPATH**/ ?>