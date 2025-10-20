<section id="faq" class="faq-area pt-90 pb-90">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="faq-wrap">
                    <div class="accordion" id="accordionExample">
                        <?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($loop->odd): ?>
                                <div class="card">
                                    <div class="card-header" id="heading<?php echo e($loop->index); ?>">
                                        <h2 class="mb-0">
                                            <button title="<?php echo BaseHelper::clean($faq->question); ?>" class="faq-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo e($loop->index); ?>">
                                                <?php echo BaseHelper::clean($faq->question); ?>

                                            </button>
                                        </h2>
                                    </div>
                                    <div id="<?php echo e("collapse$loop->index"); ?>" class="collapse" aria-labelledby="heading<?php echo e($loop->index); ?>" data-bs-parent="#accordionExample" style="">
                                        <div class="card-body">
                                            <?php echo BaseHelper::clean($faq->answer); ?>

                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="faq-wrap">
                    <div class="accordion" id="accordionExample1">
                        <?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($loop->even): ?>
                                <div class="card">
                                    <div class="card-header" id="heading<?php echo e($loop->index); ?>">
                                        <h2 class="mb-0">
                                            <button class="faq-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo e($loop->index); ?>">
                                                <?php echo BaseHelper::clean($faq->question); ?>

                                            </button>
                                        </h2>
                                    </div>
                                    <div id="<?php echo e("collapse$loop->index"); ?>" class="collapse" aria-labelledby="heading<?php echo e($loop->index); ?>" data-bs-parent="#accordionExample1" style="">
                                        <div class="card-body">
                                            <?php echo BaseHelper::clean($faq->answer); ?>

                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/partials/shortcodes/faqs/index.blade.php ENDPATH**/ ?>