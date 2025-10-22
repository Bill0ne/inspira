<section id="skill" class="skill-area p-relative fix" <?php if($backgroundColor = $shortcode->background_color): ?> style="background: <?php echo e($backgroundColor); ?>;" <?php endif; ?>>
    <?php if($backgroundImage = $shortcode->background_image): ?>
        <div class="animations-01">
            <img src="<?php echo e(RvMedia::getImageURL($backgroundImage)); ?>" alt="<?php echo e($shortcode->title); ?>" />
        </div>
    <?php endif; ?>
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="skills-content s-about-content">
                    <?php if($shortcode->title || $shortcode->subtitle): ?>
                        <div class="skills-title pb-20">
                            <?php if($subtitle = $shortcode->subtitle): ?>
                                <h5><?php echo e($subtitle); ?></h5>
                            <?php endif; ?>

                            <?php if($title = $shortcode->title): ?>
                                <h2>
                                    <?php echo BaseHelper::clean($title); ?>

                                </h2>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if($description = $shortcode->description): ?>
                        <p><?php echo BaseHelper::clean($description); ?></p>
                    <?php endif; ?>
                    <div class="skills-content s-about-content mt-10">
                        <div class="skills">
                            <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($tab['title']): ?>
                                    <div class="skill-item mb-10" style="display: flex; align-items: center; padding: 5px 0;">
                                        <svg class="check-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="flex-shrink: 0; margin-right: 12px;">
                                            <path d="M20 6L9 17L4 12" stroke="#EDBE83" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span class="skill-text" style="font-size: 16px; font-weight: 500; color: #333; line-height: 1.2;"><?php echo e($tab['title']); ?></span>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 pr-30">
                <?php if($rightImage = $shortcode->right_image): ?>
                    <div class="skills-img wow fadeInRight animated" data-animation="fadeInRight" data-delay=".4s">
                        <img src="<?php echo e(RvMedia::getImageURL($rightImage)); ?>" alt="<?php echo e($shortcode->title); ?>" class="img" />
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section><?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/partials/shortcodes/why-choose-us/index.blade.php ENDPATH**/ ?>