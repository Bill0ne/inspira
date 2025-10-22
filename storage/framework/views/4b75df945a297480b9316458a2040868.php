<section class="about-area about-p pt-90 pb-90 p-relative fix">
    <?php if($floatingRightImage = $shortcode->floating_right_image): ?>
        <div class="animations-02">
            <img src="<?php echo e(RvMedia::getImageUrl($floatingRightImage)); ?>" alt="<?php echo e($shortcode->title); ?>" />
        </div>
    <?php endif; ?>
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="s-about-img p-relative wow fadeInLeft animated" data-animation="fadeInLeft" data-delay=".4s">
                    <?php if($topLeftImage = $shortcode->top_left_image): ?>
                        <img src="<?php echo e(RvMedia::getImageUrl($topLeftImage)); ?>" alt="<?php echo e($shortcode->title); ?>" />
                    <?php endif; ?>

                    <?php if($bottomRightImage = $shortcode->bottom_right_image): ?>
                        <div class="about-icon">
                            <img src="<?php echo e(RvMedia::getImageUrl($bottomRightImage)); ?>" alt="<?php echo e($shortcode->title); ?>" />
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="about-content s-about-content wow fadeInRight animated pl-30" data-animation="fadeInRight" data-delay=".4s">
                    <div class="about-title second-title pb-25">
                        <?php if($subtitle = $shortcode->subtitle): ?>
                            <h5><?php echo e($subtitle); ?></h5>
                        <?php endif; ?>

                        <?php if($title = $shortcode->title): ?>
                            <h2><?php echo BaseHelper::clean($title); ?></h2>
                        <?php endif; ?>
                    </div>
                    <?php if($description = $shortcode->description): ?>
                        <p>
                            <?php echo BaseHelper::clean($description); ?>

                        </p>
                    <?php endif; ?>
                    <div class="about-content3 mt-30">
                        <div class="row justify-content-center align-items-center">
                            <div class="col-md-12">
                                <ul class="green mb-30">
                                    <?php $__currentLoopData = $highlightArray; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $highlight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo BaseHelper::clean($highlight); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                            <?php if($shortcode->signature_image && $shortcode->signature_author): ?>
                                <div class="col-md-12">
                                    <div class="signature">
                                        <img src="<?php echo e(RvMedia::getImageUrl($shortcode->signature_image)); ?>" alt="<?php echo e(__('Signature')); ?>" />
                                        <h3 class="mt-10"><?php echo e($shortcode->signature_author); ?></h3>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/partials/shortcodes/about-us/styles/style-2.blade.php ENDPATH**/ ?>