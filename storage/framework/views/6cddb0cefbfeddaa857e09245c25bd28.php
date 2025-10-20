<?php
    $couponCode = \Botble\Hotel\Facades\HotelHelper::getCheckoutData('coupon_code');
 $couponAmount = \Botble\Hotel\Facades\HotelHelper::getCheckoutData('coupon_amount');
?>
<div class="order-detail-box http://botble.hotelbooking.com/de/coupon/refresh?coupon_code=00000mb-20" data-refresh-url="<?php echo e(route('coupon.course.refresh')); ?>">
    <button class="btn-link ps-0 text-decoration-none toggle-coupon-form" type="button"><?php echo e(trans('plugins/hotel::coupon.toggle_coupon_form_text')); ?></button>

    <div class="card coupon-form mt-3" style="<?php echo \Illuminate\Support\Arr::toCssStyles(['display: none' => ! ($couponCode && $couponAmount)]) ?>">
        <div class="card-body">
            <?php if($couponCode && $couponAmount): ?>
                <div class="d-flex align-items-center justify-content-between alert alert-success mb-0 w-100">
                    <span><?php echo e(__('Coupon code: :code', ['code' => $couponCode])); ?></span>
                    <input name="coupon_hidden" type="hidden" value="<?php echo e(! empty($couponCode)); ?>" />

                    <button class="btn btn-link text-decoration-none remove-coupon-code" data-url="<?php echo e(route('coupon.course.remove')); ?>" type="button">
                        <?php if (isset($component)) { $__componentOriginal73995948b3bd877b76251b40caf28170 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal73995948b3bd877b76251b40caf28170 = $attributes; } ?>
<?php $component = Botble\Icon\View\Components\Icon::resolve(['name' => 'ti ti-trash'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Botble\Icon\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal73995948b3bd877b76251b40caf28170)): ?>
<?php $attributes = $__attributesOriginal73995948b3bd877b76251b40caf28170; ?>
<?php unset($__attributesOriginal73995948b3bd877b76251b40caf28170); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal73995948b3bd877b76251b40caf28170)): ?>
<?php $component = $__componentOriginal73995948b3bd877b76251b40caf28170; ?>
<?php unset($__componentOriginal73995948b3bd877b76251b40caf28170); ?>
<?php endif; ?>
                        <?php echo e(__('Remove')); ?>

                    </button>
                </div>
            <?php else: ?>
                <div class="form-group">
                    <label for="coupon_code" class="form-label"><?php echo e(trans('plugins/hotel::coupon.coupon_code')); ?></label>
                    <div class="input-group">
                        <input type="text" id="coupon_code" name="coupon_code" class="form-control" placeholder="<?php echo e(trans('plugins/hotel::coupon.coupon_code_placeholder')); ?>" value="<?php echo e(BaseHelper::clean(old('coupon_code'))); ?>">
                        <button class="btn btn-primary apply-coupon-code" data-url="<?php echo e(route('coupon.course.apply')); ?>" type="button">
                            <?php echo e(trans('plugins/hotel::coupon.apply_coupon_code')); ?>

                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/plugins/courses/resources/views/coupons/partials/form.blade.php ENDPATH**/ ?>