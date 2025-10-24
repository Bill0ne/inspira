<section class="video-area pt-150 pb-150 p-relative"
         <?php if($backgroundImage = $shortcode->background_image): ?>
             style="background-image:url('<?php echo e(RvMedia::getImageUrl($backgroundImage)); ?>'); background-repeat: no-repeat; background-position: center bottom; background-size:cover;"
         <?php endif; ?>
    >

    <div class="content-lines-wrapper2">
        <div class="content-lines-inner2">
            <div class="content-lines2"></div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="s-video-wrap">
                    <?php if($youtubeVideoId = $shortcode->youtube_video_id ): ?>
                        <div class="s-video-content">
                            <a href="https://www.youtube.com/watch?v=<?php echo e($youtubeVideoId); ?>" class="popup-video">
                                <?php
                                    $buttonIcon = $shortcode->button_icon ?
                                            RvMedia::getImageUrl($shortcode->button_icon) :
                                            Theme::asset()->url('/images/play-button.png')
                                ?>
                                <img src="<?php echo e($buttonIcon); ?>" alt="<?php echo e(__('Button play')); ?>">
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="section-title center-align text-center">
                    <?php if($title = $shortcode->title): ?>
                        <h2><?php echo BaseHelper::clean($title); ?></h2>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/partials/shortcodes/intro-video/index.blade.php ENDPATH**/ ?>