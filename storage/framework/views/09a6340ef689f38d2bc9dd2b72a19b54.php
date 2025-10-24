<?php ($bgColor = $shortcode->background_color ?: '#f7f5f1'); ?>

<section class="feature-area2 p-relative fix" style="background: <?php echo e($bgColor); ?>">
    <?php if($bgImage = $shortcode->background_image): ?>
        <div class="animations-02">
            <img src="<?php echo e(RvMedia::getImageUrl($bgImage)); ?>" alt="<?php echo e(__('Background image')); ?>">
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-6 col-md-12 col-sm-12 pr-30">
                <?php if($image = $shortcode->image): ?>
                    <div class="feature-img">
                        <img src="<?php echo e(RvMedia::getImageUrl($image)); ?>" alt="<?php echo e(__('Image')); ?>" class="img">
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="feature-content s-about-content">
                    <div class="feature-title pb-20">
                        <?php if($subtitle = $shortcode->subtitle): ?>
                            <h5><?php echo BaseHelper::clean($subtitle); ?></h5>
                        <?php endif; ?>

                        <?php if($title = $shortcode->title): ?>
                            <h2><?php echo BaseHelper::clean($title); ?></h2>
                        <?php endif; ?>
                    </div>

                    <?php if($description = $shortcode->description): ?>
                        <p><?php echo BaseHelper::clean($description); ?></p>
                    <?php endif; ?>

                    <?php if(($buttonPrimaryLabel = ($shortcode->button_primary_label ?: $shortcode->button_label)) && ($buttonPrimaryUrl = ($shortcode->button_primary_url ?: $shortcode->button_url))): ?>
                        <a href="<?php echo e($buttonPrimaryUrl); ?>" class="btn ss-btn smoth-scroll"><?php echo BaseHelper::clean($buttonPrimaryLabel); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/partials/shortcodes/feature-area/index.blade.php ENDPATH**/ ?>