<?php echo Html::link(route('coupons.edit', $coupon), $coupon->code); ?>

<p class="text-muted mt-1 mb-0"><?php echo BaseHelper::clean(trans('plugins/hotel::coupon.value_off', ['value' => "<strong>$value</strong>"])); ?></p>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/plugins/hotel/resources/views/coupons/partials/detail.blade.php ENDPATH**/ ?>