<?php
    Theme::asset()->container('footer')->usePath()->add('date-css', 'css/date.css');
        Theme::asset()->container('footer')->add('popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js', ['jquery']);
        Theme::asset()->container('footer')->add('bootstrap-js', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.bundle.min.js', ['jquery', 'popper']);
      Theme::asset()->container('footer')->usePath()->add('moment-js', 'vendors/moment.min.js');
      Theme::asset()->container('footer')->usePath()->add('date-picker', 'vendors/date-picker.min.js');
        Theme::asset()->container('footer')->usePath()->add('datetime-js', 'js/datetime.js');
?>
<section class="booking pt-90 pb-90 p-relative fix">
    <?php if($shapeImage = $shortcode->shape_image): ?>
        <div class="animations-01">
            <img src="<?php echo e(RvMedia::getImageUrl($shapeImage)); ?>" alt="<?php echo e(__('Shape image')); ?>">
        </div>
    <?php endif; ?>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-6">
                <div class="contact-bg02">
                    <div class="section-title center-align">
                        <?php if($subtitle = $shortcode->subtitle): ?>
                            <h5><?php echo BaseHelper::clean($subtitle); ?></h5>
                        <?php endif; ?>
                        <?php if($title = $shortcode->title): ?>
                            <h2><?php echo BaseHelper::clean($title); ?></h2>
                        <?php endif; ?>
                    </div>
                    <form action="<?php echo e(route('public.booking')); ?>" method="post" class="contact-form mt-30 form-booking">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div id="booking-slots" class="booking-slots-wrapper">
                                <div class="slot-item0 slot-item">
                                    <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <div class="contact-field p-relative c-name mb-20">
                                    <label for="booking-form-start-date"><i
                                            class="fal fa-badge-check"></i><?php echo e(__('Check In Date')); ?></label>
                                    <input
                                            type="text"
                                            name="slots[0][start_date]"
                                            class="theme-date-input-start check-in"
                                            id="checkin-0"
                                            autocomplete="off"
                                            placeholder="DD / MM / YYYY  HH : MM"
                                           value="<?php echo e(old('start_date', Carbon\Carbon::now()->format(HotelHelper::getDateFormat()))); ?>"
                                    >
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6">
                                <div class="contact-field p-relative c-subject mb-20">
                                    <label for="booking-form-end-date"><i
                                            class="fal fa-times-octagon"></i><?php echo e(__('Check Out Date')); ?></label>
                                    <input
                                            type="text"
                                            name="slots[0][end_date]"
                                            class="theme-date-input-end check-out"
                                            id="checkout-0"
                                            autocomplete="off"
                                            placeholder="DD / MM / YYYY  HH : MM"
                                           value="<?php echo e(BaseHelper::stringify(old('end_date', Carbon\Carbon::now()->addDay()->format(HotelHelper::getDateFormat())))); ?>">
                                </div>
                            </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="contact-field p-relative c-subject mb-20">
                                    <label for="adults"><i class="fal fa-users"></i><?php echo e(__('Guests')); ?></label>
                                    <select name="adults" id="adults">
                                        <?php for($i = 1; $i <= 14; $i++): ?>
                                            <option value="<?php echo e($i); ?>" <?php if(old('adults', HotelHelper::getMinimumNumberOfGuests()) === 1): echo 'selected'; endif; ?>><?php echo e($i); ?> <?php echo e($i == 1 ? __('Guest') : __('Guests')); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="contact-field p-relative c-option mb-20">
                                    <label for="room"><i class="fal fa-concierge-bell"></i><?php echo e(__('Room')); ?></label>
                                    <select name="room_id" id="room">
                                        <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option <?php if(old('room_id') === $key): echo 'selected'; endif; ?> value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="slider-btn mt-15">
                                    <button type="submit" class="btn ss-btn" data-animation="fadeInRight"
                                            data-delay=".8s">
                                        <span><?php echo e(__('Book now')); ?></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <?php if($image = $shortcode->image): ?>
                    <div class="booking-img">
                        <img src="<?php echo e(RvMedia::getImageUrl($image)); ?>" alt="<?php echo e(__('Image')); ?>">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/partials/shortcodes/booking-form/index.blade.php ENDPATH**/ ?>