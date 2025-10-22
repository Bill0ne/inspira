<?php
    Theme::asset()->container('footer')->usePath()->add('lightgallery-css', 'plugins/lightgallery/css/lightgallery.min.css');
    Theme::asset()->container('footer')->usePath()->add('lightgallery-js', 'plugins/lightgallery/js/lightgallery.min.js');

    Theme::set('pageTitle', $room->name);
    $nights = $startDate->diffInHours($endDate);
?>
<div class="about-area5 about-p p-relative room-details">
    <div class="container pt-60 pb-40">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-4 order-2">
                <aside class="sidebar services-sidebar">
                    <?php if(HotelHelper::isBookingEnabled()): ?>
                        <div class="sidebar-widget categories">
                            <div class="widget-content">
                                <h2 class="widget-title"> <?php echo e(__('Booking form')); ?> </h2>
                                <div class="booking">
                                    <div class="contact-bg">
                                        <?php echo Theme::partial('hotel.forms.form', ['availableForBooking' => true, 'style' => 1, 'room' => $room]); ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php echo dynamic_sidebar('room_sidebar'); ?>

                </aside>
            </div>

            <div class="col-lg-8 col-md-12 col-sm-12 order-1">
                <div class="service-detail">
                    <div class="thumb">
                        <div class="room-details-slider">
                            <?php $__currentLoopData = $room->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(RvMedia::getImageUrl($img)); ?>">
                                    <img src="<?php echo e(RvMedia::getImageUrl($img, 'room-image')); ?>" alt="<?php echo e($room->name); ?>">
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="room-details-slider-nav">
                            <?php $__currentLoopData = $room->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <img src="<?php echo e(RvMedia::getImageUrl($img, 'thumb')); ?>" alt="<?php echo e($room->name); ?>">
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="content-box">
<div class="row align-items-center mb-50">
    <div class="col-12">
        <div class="price">
            <h2><?php echo e($room->name); ?></h2>
            
            <?php if(auth('customer')->check() || auth()->check()): ?>
                <?php if($nights > 1): ?>
                    <span><?php echo e(__(':price for :hours hours', ['price' => format_price($room->getRoomTotalPrice($startDate, $endDate)), 'hours' => $nights])); ?></span>
                <?php else: ?>
                    <span><?php echo e(__(':price for :hours hour', ['price' => format_price($room->getRoomTotalPrice($startDate, $endDate)), 'hours' => $nights])); ?></span>
                <?php endif; ?>

            <?php else: ?>
                <span class="text-muted"><?php echo e(__('Bitte einloggen um die Preise zu sehen')); ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>

                        <?php echo BaseHelper::clean($room->content); ?>


                        <?php if($room->amenities->isNotEmpty()): ?>
                            <div class="room-block-content shadow-block mt-50 amenities-list">
                                <h3><?php echo e(__('Amenities')); ?></h3>
                                <div class="row">
                                    <?php $__currentLoopData = $room->amenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $image = $amenity->getMetaData('icon_image', true)
                                        ?>

                                        <div class="col-xl-4 col-lg-6 col-12 d-flex align-items-center mb-3">
                                            <?php if($image): ?>
                                                <img width="20px" class="d-block" src="<?php echo e(RvMedia::getImageUrl($image)); ?>" alt="<?php echo e($amenity->name); ?>">
                                            <?php elseif($amenity->icon): ?>
                                                <?php if (isset($component)) { $__componentOriginal73995948b3bd877b76251b40caf28170 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal73995948b3bd877b76251b40caf28170 = $attributes; } ?>
<?php $component = Botble\Icon\View\Components\Icon::resolve(['name' => $amenity->icon] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                                            <?php endif; ?>
                                            <span class="ms-2"><?php echo e($amenity->name); ?></span>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($rules = theme_option('hotel_rules')): ?>
                            <div class="room-block-content shadow-block">
                                <div class="hotel-rules-box">
                                    <h3><?php echo e(__('Hotel Rules')); ?></h3>
                                    <?php echo BaseHelper::clean($rules); ?>

                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($cancellation = theme_option('cancellation')): ?>
                            <div class="room-block-content shadow-block">
                                <h3><?php echo e(__('Cancellation')); ?></h3>
                                <?php echo BaseHelper::clean($cancellation); ?>

                            </div>
                        <?php endif; ?>

                        <?php if(HotelHelper::isReviewEnabled()): ?>
                            <?php echo $__env->make(Theme::getThemeNamespace('views.hotel.partials.reviews'), ['model' => $room], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endif; ?>

                        <div class="content-box related-room">
                            <h3><?php echo e(__('Related Rooms')); ?></h3>
                            <div class="row">
                                <?php $__currentLoopData = $relatedRooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-lg-6 mb-20">
                                        <?php echo Theme::partial('rooms.item', compact('room', 'startDate', 'endDate', 'nights', 'adults')); ?>

                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/views/hotel/room.blade.php ENDPATH**/ ?>