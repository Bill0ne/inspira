<div class="sidebar-widget categories check-availability-custom">
    <div class="widget-content">
        <?php if($title = $config['title']): ?>
            <h2 class="widget-title"> <?php echo BaseHelper::clean($title); ?>  </h2>
        <?php endif; ?>
        <div class="booking">
            <div class="contact-bg">
                <?php echo Theme::partial('hotel.forms.form', ['style' => 1, 'availableForBooking' => false]); ?>

            </div>
        </div>
    </div>
</div>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/////widgets/check-availability-form/templates/frontend.blade.php ENDPATH**/ ?>