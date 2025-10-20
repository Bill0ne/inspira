<footer class="footer-bg footer-p">
    <div class="footer-top  pt-90 pb-40"
         <?php if($background = theme_option('background_footer')): ?>
             style="background-image: url('<?php echo e(RvMedia::getImageUrl($background)); ?>');"
         <?php endif; ?>
    >
        <div class="container">
            <div class="row justify-content-between">
                <?php echo dynamic_sidebar('footer_sidebar'); ?>

            </div>
        </div>
    </div>
    <div class="copyright-wrap">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-6">
                    <?php echo e(theme_option('copyright')); ?>

                </div>
                <div class="col-lg-6 col-md-6 text-end text-xl-right">
                    <?php if($socialLinks = json_decode(theme_option('social_links'))): ?>
                        <div class="footer-social">
                            <?php $__currentLoopData = $socialLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php ($social = collect($social)->pluck('value', 'key')); ?>
                                <a target="_blank" href="<?php echo e($social->get('url')); ?>" title="<?php echo e($social->get('name')); ?>"><i class="<?php echo e($social->get('social-icon')); ?>"></i></a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</footer>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/partials/footer.blade.php ENDPATH**/ ?>