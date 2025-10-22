<?php
    Theme::set('pageTitle',  $gallery->name);
?>

<?php if(function_exists('get_galleries')): ?>
    <div class="container mt-50 mb-50">
        <h6 class="custom-gallery-description text-center"><?php echo BaseHelper::clean($gallery->description); ?></h6>
        <div class="row mt-50">
            <article class="post post--single">
                <div class="post__content">
                    <div class="row" id="list-photo">
                        <?php $__currentLoopData = gallery_meta_data($gallery); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($image): ?>
                                <div class="col-12 col-md-4 mt-20" data-src="<?php echo e(RvMedia::getImageUrl(Arr::get($image, 'img'), 'galleries')); ?>" data-sub-html="<?php echo e(BaseHelper::clean(Arr::get($image, 'description'))); ?>">
                                    <div class="photo-item">
                                        <div class="thumb">
                                            <a href="<?php echo e(BaseHelper::clean(Arr::get($image, 'description'))); ?>">
                                                <img src="<?php echo e(RvMedia::getImageUrl(Arr::get($image, 'img'), 'galleries')); ?>" alt="<?php echo e(BaseHelper::clean(Arr::get($image, 'description'))); ?>">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </article>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/views/gallery.blade.php ENDPATH**/ ?>