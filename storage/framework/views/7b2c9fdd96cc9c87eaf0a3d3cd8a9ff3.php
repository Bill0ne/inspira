<div class="service-detail-contact wow fadeup-animation" data-wow-delay="1.1s">
    <?php if($title = $config['title']): ?>
        <h3 class="h3-title"><?php echo BaseHelper::clean($title); ?></h3>
    <?php endif; ?>

    <?php if($phone = $config['phone']): ?>
        <a href="tel:<?php echo e($phone); ?>" title="<?php echo e(__('Call now')); ?>"><?php echo e($phone); ?></a>
    <?php endif; ?>
</div>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/////widgets/room-contact/templates/frontend.blade.php ENDPATH**/ ?>