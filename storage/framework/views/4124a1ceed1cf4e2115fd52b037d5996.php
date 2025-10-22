<section class="newslater-area p-relative pt-90 pb-90" <?php if($backgroundColor = $shortcode->background_color): ?> style="background-color: <?php echo e($backgroundColor); ?>;" <?php endif; ?>>
    <?php if($floatingImage = $shortcode->left_floating_image): ?>
        <div class="animations-01">
            <img src="<?php echo e(RvMedia::getImageURL($floatingImage)); ?>" alt="<?php echo e($shortcode->title); ?>">
        </div>
    <?php endif; ?>
    <div class="container">
        <div class="row justify-content-center align-items-center text-center">
            <div class="col-xl-9 col-lg-9">
                <div class="section-title center-align mb-40 text-center wow fadeInDown animated" data-animation="fadeInDown" data-delay=".4s">
                    <?php if($subtitle = $shortcode->subtitle): ?>
                        <h5><?php echo e($subtitle); ?></h5>
                    <?php endif; ?>

                    <?php if($title = $shortcode->title): ?>
                        <h2>
                            <?php echo BaseHelper::clean($title); ?>

                        </h2>
                    <?php endif; ?>

                    <?php if($description = $shortcode->description): ?>
                        <p><?php echo BaseHelper::clean($description); ?></p>
                    <?php endif; ?>
                </div>
                <form name="ajax-form" id="contact-form4" dir="ltr" action="<?php echo e(route('public.newsletter.subscribe')); ?>" method="POST" class="newslater newsletter-form">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <input class="form-control" id="email" name="email" type="email" placeholder="<?php echo e(__('Your Email Address')); ?>" required>
                        <button type="submit" class="btn btn-custom" id="send2"><?php echo e(__('Subscribe Now')); ?></button>
                    </div>
                    <?php echo apply_filters('form_extra_fields_render', null, \Botble\Newsletter\Forms\Fronts\NewsletterForm::class); ?>

                </form>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/partials/shortcodes/newsletter/index.blade.php ENDPATH**/ ?>