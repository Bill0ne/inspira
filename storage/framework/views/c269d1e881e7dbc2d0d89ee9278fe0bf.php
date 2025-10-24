<div class="col-xl-4 col-lg-4 col-sm-6">
    <div class="footer-widget mb-30">
        <?php if($logo = theme_option('logo')): ?>
            <div class="f-widget-title mb-30">
                <img src="<?php echo e(Rvmedia::getImageUrl($logo)); ?>" alt="<?php echo e(theme_option('site_name')); ?>">
            </div>
        <?php endif; ?>
        <div class="f-contact">
            <ul>
                <?php if($phoneNumber): ?>
                    <li>
                        <i class="icon fal fa-phone"></i>
                        <span><?php echo BaseHelper::clean($phoneNumber); ?></span>
                    </li>
                <?php endif; ?>

                <?php if($email): ?>
                    <li>
                        <i class="icon fal fa-envelope"></i>
                        <span><?php echo BaseHelper::clean($email); ?></span>
                    </li>
                <?php endif; ?>

                <?php if($address): ?>
                    <li>
                        <i class="icon fal fa-map-marker-check"></i>
                        <span><?php echo BaseHelper::clean($address); ?></span>
                    </li>
                <?php endif; ?>
            </ul>

        </div>
    </div>
</div>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/////widgets/contact-information/templates/frontend.blade.php ENDPATH**/ ?>