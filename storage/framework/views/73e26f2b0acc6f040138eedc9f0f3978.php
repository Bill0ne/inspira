<section id="home" class="slider-area fix p-relative">
    <div class="slider-active" style="background: #101010;">
        <?php $__currentLoopData = $sliders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="single-slider slider-bg d-flex align-items-center" style="background-image:url(<?php echo e(RvMedia::getImageUrl($slider->image)); ?>); background-size: cover;">
                <div class="container">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-lg-7 col-md-7">
                            <div class="slider-content s-slider-content mt-80 text-center">
                                <?php if($title = $slider->title): ?>
                                    <h2 data-animation="fadeInUp" data-delay=".4s"><?php echo BaseHelper::clean($title); ?></h2>
                                <?php endif; ?>

                                <?php if($description = $slider->description): ?>
                                    <p data-animation="fadeInUp" data-delay=".6s"><?php echo BaseHelper::clean($description); ?></p>
                                <?php endif; ?>

                                <div class="slider-btn mt-30 mb-105">
                                    <?php
                                        $buttonPrimaryLabel = $slider->getMetaData('button_primary_label', true);
                                        $buttonPrimaryUrl = $slider->getMetaData('button_primary_url', true);
                                        $buttonPlayLabel = $slider->getMetaData('button_play_label', true);
                                        $linkYoutubeUrl = $slider->getMetaData('youtube_url', true);

                                        if ($linkYoutubeUrl) {
                                            $linkYoutubeUrl = Botble\Theme\Supports\Youtube::getYoutubeVideoID($linkYoutubeUrl);
                                        }

                                    ?>

                                    <?php if($buttonPrimaryUrl && $buttonPrimaryLabel): ?>
                                        <a href="<?php echo e($buttonPrimaryUrl); ?>" class="btn ss-btn active mr-15" data-animation="fadeInLeft" data-delay=".4s">
                                            <?php echo BaseHelper::clean($buttonPrimaryLabel); ?>

                                        </a>
                                    <?php endif; ?>

                                    <?php if($buttonPlayLabel && $linkYoutubeUrl): ?>
                                        <a href="https://www.youtube.com/watch?v=<?php echo e($linkYoutubeUrl); ?>" class="video-i popup-video" data-animation="fadeInUp" data-delay=".8s" style="animation-delay: 0.8s;" tabindex="0">
                                            <i class="fas fa-play"></i>
                                            <?php echo BaseHelper::clean($buttonPlayLabel); ?>

                                        </a>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/partials/shortcodes/simple-slider/index.blade.php ENDPATH**/ ?>