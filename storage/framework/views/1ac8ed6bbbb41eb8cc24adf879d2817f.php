<section id="contact" class="contact-area after-none contact-bg  pb-90 p-relative fix" style="margin-top: -90px;">
    <div class="container">
        <!-- Kontaktformular oben mittig -->
        <div class="row justify-content-center mb-60">
            <div class="col-lg-8 col-md-10">
                <div class="contact-bg02">
                    <?php if($title = $shortcode->title): ?>
                        <div class="section-title center-align mb-40 text-center wow fadeInDown animated" data-animation="fadeInDown" data-delay=".4s">
                            <h2>
                                <?php echo BaseHelper::clean($title); ?>

                            </h2>
                        </div>
                    <?php endif; ?>
                    <?php echo $form->renderForm(); ?>

                </div>
            </div>
        </div>

        <!-- Kontaktdaten in einer Zeile mit Rahmen -->
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="row justify-content-center">
                    <?php if($shortcode->address_label || $shortcode->address_detail): ?>
                        <div class="col-auto mb-30">
                            <div class="single-cta-box text-center p-4 wow fadeInUp animated" data-animation="fadeInDown animated" data-delay=".2s" style="border: 2px solid #578E88; border-radius: 15px; width: 275px; height: 275px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                <?php if($addressIcon = $shortcode->address_icon): ?>
                                    <div class="cta-icon mb-3">
                                        <i class="<?php echo e($addressIcon); ?>" style="color: #578E88; font-size: 32px;"></i>
                                    </div>
                                <?php endif; ?>

                                <?php if($addressLabel = $shortcode->address_label): ?>
                                    <h5 class="mb-2"><?php echo e($addressLabel); ?></h5>
                                <?php endif; ?>

                                <?php if($addressDetail = $shortcode->address_detail): ?>
                                    <p class="mb-0">
                                        <?php echo BaseHelper::clean($addressDetail); ?>

                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($shortcode->work_time_label || $shortcode->work_time_detail): ?>
                        <div class="col-auto mb-30">
                            <div class="single-cta-box text-center p-4 wow fadeInUp animated" data-animation="fadeInDown animated" data-delay=".3s" style="border: 2px solid #578E88; border-radius: 15px; width: 275px; height: 275px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                <?php if($workTimeIcon = $shortcode->work_time_icon): ?>
                                    <div class="cta-icon mb-3">
                                        <i class="<?php echo e($workTimeIcon); ?>" style="color: #578E88; font-size: 32px;"></i>
                                    </div>
                                <?php endif; ?>

                                <?php if($workTimeLabel = $shortcode->work_time_label): ?>
                                    <h5 class="mb-2"><?php echo e($workTimeLabel); ?></h5>
                                <?php endif; ?>

                                <?php if($workTimeDetail = $shortcode->work_time_detail): ?>
                                    <p class="mb-0">
                                        <?php echo BaseHelper::clean($workTimeDetail); ?>

                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($shortcode->phone_label && $shortcode->phone_detail): ?>
                        <div class="col-auto mb-30">
                            <div class="single-cta-box text-center p-4 wow fadeInUp animated" data-animation="fadeInDown animated" data-delay=".4s" style="border: 2px solid #578E88; border-radius: 15px; width: 275px; height: 275px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                <?php if($phoneIcon = $shortcode->phone_icon): ?>
                                    <div class="cta-icon mb-3">
                                        <i class="<?php echo e($phoneIcon); ?>" style="color: #578E88; font-size: 32px;"></i>
                                    </div>
                                <?php endif; ?>

                                <?php if($phoneLabel = $shortcode->phone_label): ?>
                                    <h5 class="mb-2"><?php echo e($phoneLabel); ?></h5>
                                <?php endif; ?>

                                <?php if($phoneDetail = $shortcode->phone_detail): ?>
                                    <p class="mb-0">
                                        <?php echo BaseHelper::clean($phoneDetail); ?>

                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($shortcode->email_label || $shortcode->email_detail): ?>
                        <div class="col-auto mb-30">
                            <div class="single-cta-box text-center p-4 wow fadeInUp animated" data-animation="fadeInDown animated" data-delay=".5s" style="border: 2px solid #578E88; border-radius: 15px; width: 275px; height: 275px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                <?php if($emailIcon = $shortcode->email_icon): ?>
                                    <div class="cta-icon mb-3">
                                        <i class="<?php echo e($emailIcon); ?>" style="color: #578E88; font-size: 32px;"></i>
                                    </div>
                                <?php endif; ?>

                                <?php if($emailLabel = $shortcode->email_label): ?>
                                    <h5 class="mb-2"><?php echo e($emailLabel); ?></h5>
                                <?php endif; ?>

                                <?php if($emailDetail = $shortcode->email_detail): ?>
                                    <p class="mb-0">
                                        <?php echo BaseHelper::clean($emailDetail); ?>

                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section><?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/partials/shortcodes/contact-form/index.blade.php ENDPATH**/ ?>