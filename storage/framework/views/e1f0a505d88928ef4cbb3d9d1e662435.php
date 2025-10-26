<?php
    Theme::set('pageTitle', __('Booking information'));
    Theme::layout('full-width');
?>

<section class="booking-information-page">
    <div class="pt-60 pb-60">
        <div class="container">
            <div class="justify-content-center">
                <div class="booking-form-body room-details booking-information">
                    <h3 class="mb-20"><?php echo e(__('Your booking information')); ?></h3>
                    <br>
                    <?php echo $__env->make('plugins/hotel::booking-info', ['booking' => $booking, 'route' => 'customer.generate-invoice'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/views/hotel/booking-information.blade.php ENDPATH**/ ?>