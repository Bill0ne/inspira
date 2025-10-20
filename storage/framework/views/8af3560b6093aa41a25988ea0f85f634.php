<?php
    $breadcrumbBackgroundImage = Theme::get('breadcrumbBackgroundImage') ?: theme_option('breadcrumb_background_image');
    $bgImage = $breadcrumbBackgroundImage ? RvMedia::getImageUrl($breadcrumbBackgroundImage) : Theme::asset()->url('images/breadcrumb-bg.jpg');
?>

<section class="breadcrumb-area d-flex align-items-center" style="background-image:url(<?php echo e($bgImage); ?>);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-12 col-lg-12">
                <div class="breadcrumb-wrap text-center">
                    <div class="breadcrumb-title">
                        <?php if($pageTitle = Theme::get('pageTitle')): ?>
                            <h2><?php echo BaseHelper::clean($pageTitle); ?></h2>
                        <?php endif; ?>

                        <?php if($crumbs = Theme::breadcrumb()->getCrumbs()): ?>
                                <div class="breadcrumb-wrap">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <?php $__currentLoopData = $crumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $crumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(! $loop->last): ?>
                                                    <li class="breadcrumb-item"><a href="<?php echo e($crumb['url']); ?>"><?php echo e($crumb['label']); ?></a></li>
                                                <?php else: ?>
                                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e($crumb['label']); ?></li>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ol>
                                    </nav>
                                </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/partials/breadcrumbs.blade.php ENDPATH**/ ?>