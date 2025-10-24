<?php if(is_plugin_active('newsletter')): ?>
    <div class="col-xl-4 col-lg-4 col-sm-6">
        <div class="footer-widget mb-30">
            <?php if($title = $config['title']): ?>
                <div class="f-widget-title">
                    <h2><?php echo BaseHelper::clean($title); ?></h2>
                </div>
            <?php endif; ?>

            <div class="footer-link" dir="ltr">
                <div class="subricbe p-relative form-newsletter" data-animation="fadeInDown" data-delay=".4s" >
                    <?php echo $form->renderForm(); ?>

                </div>
            </div>

        </div>
    </div>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/////widgets/newsletter-subscribe/templates/frontend.blade.php ENDPATH**/ ?>