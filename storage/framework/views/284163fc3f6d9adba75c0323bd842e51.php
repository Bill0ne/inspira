<?php if(isset($galleries) && !$galleries->isEmpty()): ?>
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="my-masonry text-center mb-50">
                <div class="button-group filter-button-group">
                    <button class="active" data-filter="*"><?php echo e(__('All')); ?></button>
                    <?php $__currentLoopData = $galleries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gallery): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $galleryName = $gallery->name;
                            $galleryClass = '.' . str_replace(' ', '', strtolower($gallery->name));
                        ?>
                        <button data-filter="<?php echo e($galleryClass); ?>"><?php echo e($galleryName); ?></button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="masonry-gallery-huge">
                <div class="grid">
                    <div class="gallery-wrap">
                        <?php $__currentLoopData = $galleries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gallery): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $galleryName = $gallery->name;
                                $galleryClass = str_replace(' ', '', strtolower($gallery->name));
                            ?>
                            <div class="grid-item <?php echo e($galleryClass); ?>">
                                <a href="<?php echo e($gallery->url); ?>">
                                    <figure class="gallery-image">
                                        <img src="<?php echo e(RvMedia::getImageUrl($gallery->image, 'medium')); ?>" alt="<?php echo e($gallery->name); ?>" class="img" />
                                    </figure>
                                </a>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/partials/gallery/galleries.blade.php ENDPATH**/ ?>