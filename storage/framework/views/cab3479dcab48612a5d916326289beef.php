<main class="container">
    <div class="form-group mb-3">
        <label for="seo_title" class="control-label"><?php echo e(trans('plugins/vig-seo::vig-seo.focus_keyphrase')); ?></label>
        <?php echo Form::text('vig_seo_keywords', $meta ?? old('vig_seo_keywords'), ['class' => 'form-control', 'id' => 'seo_title', 'placeholder' => 'Focus keyphrase', 'data-counter' => 300]); ?>

        <?php echo e(Form::helper(trans('plugins/vig-seo::vig-seo.focus_keyphrase_helper'))); ?>

    </div>

    <div class="p-3 my-3 shadow-sm bg-blue-chambray-opacity">
        <div class="lh-1">
            <h1 class="h6 text-white mb-0 lh-1"><?php echo e(trans('plugins/vig-seo::vig-seo.keyword_density')); ?></h1>
            <small class="text-white"><?php echo e(trans('plugins/vig-seo::vig-seo.keyword_density_helper')); ?></small>
        </div>

        <ul class="list-group mt-2">
            <?php $__currentLoopData = $data['getKeywords']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $keyword): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(!empty($key)): ?>
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                        <div>
                            <h6 class="my-0"><?php echo e($key); ?></h6>
                        </div>
                        <span class="text-muted"><?php echo e(trans('plugins/vig-seo::vig-seo.number_of_occurrences')); ?>: <?php echo e(count($keyword)); ?></span>
                    </li>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>

    </div>

    <div class="p-3 my-3 shadow-sm bg-blue-chambray-opacity">
        <div class="lh-1">
            <h1 class="h6 text-white mb-0 lh-1"><?php echo e(trans('plugins/vig-seo::vig-seo.keywords_suggest')); ?></h1>
            <small class="text-white"><?php echo e(trans('plugins/vig-seo::vig-seo.keywords_suggest_helper')); ?></small>
        </div>


        <?php $__currentLoopData = $data['keywords']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $keywords): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <span class="badge badge-pill badge-info"><?php echo e($key); ?> (<?php echo e(count($keywords)); ?>)</span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


    </div>


    <div class="d-flex align-items-center p-3 my-3 text-white bg-purple shadow-sm">
        <div class="lh-1">
            <h1 class="h6 mb-0 text-white lh-1"><?php echo e(trans('plugins/vig-seo::vig-seo.code_to_text_ratio')); ?>: <?php echo e(number_format($data['mainText']['code_to_text_ratio'])); ?>%</h1>
            <small><?php echo e(trans('plugins/vig-seo::vig-seo.code_to_text_ratio_helper')); ?></small>
        </div>
    </div>

    <div class="d-flex align-items-center p-3 my-3 text-white bg-purple shadow-sm">
        <div class="lh-1">
            <h1 class="h6 text-white mb-0 lh-1"><?php echo e(trans('plugins/vig-seo::vig-seo.word_count')); ?>: <?php echo e(number_format($data['mainText']['word_count'])); ?></h1>
            <small><?php echo e(trans('plugins/vig-seo::vig-seo.word_count_helper')); ?></small>
        </div>
    </div>

    <div class="p-3 my-3 shadow-sm bg-blue-chambray-opacity">
        <div class="lh-1">
            <h1 class="h6 text-white mb-0 lh-1"><?php echo e(trans('plugins/vig-seo::vig-seo.headers')); ?></h1>
            <small class="text-white"><?php echo e(trans('plugins/vig-seo::vig-seo.headers_helper')); ?></small>
        </div>

        <ul class="list-group mt-2">
            <?php $__currentLoopData = $data['headers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                        <h6 class="my-0"><?php echo e($key); ?></h6>
                        <?php $__currentLoopData = $header; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <small class="text-muted">- <?php echo $value['text']; ?></small><br />
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>

    <div class="p-3 my-3 shadow-sm bg-blue-chambray-opacity">
        <div class="lh-1">
            <h1 class="h6 text-white mb-0 lh-1"><?php echo e(trans('plugins/vig-seo::vig-seo.links')); ?></h1>
            <small class="text-white"><?php echo e(trans('plugins/vig-seo::vig-seo.links_helper')); ?></small>
        </div>

        <ul class="list-group mt-2">
            <?php $__currentLoopData = $data['links']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $links): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                        <h6 class="my-0"><?php echo e($key); ?> (<?php echo e(count($links)); ?>)</h6>
                        <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <small class="text-muted">- <?php echo e($value); ?></small><br />
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>

    <div class="p-3 my-3 shadow-sm bg-blue-chambray-opacity">
        <div class="lh-1">
            <h1 class="h6 text-white mb-0 lh-1"><?php echo e(trans('plugins/vig-seo::vig-seo.images')); ?></h1>
            <small class="text-white"><?php echo e(trans('plugins/vig-seo::vig-seo.images_helper')); ?></small>
        </div>

        <ul class="list-group mt-2">
            <?php $__currentLoopData = $data['images']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $images): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                        <h6 class="my-0"><?php echo e($key); ?> (<?php echo e(count($images)); ?>)</h6>
                        <?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <small class="text-muted">- src: <?php echo e($value['src']); ?> <?php echo e(!empty($value['alt']) ? '- alt: ' . $value['alt'] : ''); ?></small><br />
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>

</main>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/plugins/vig-seo/resources/views/score-box.blade.php ENDPATH**/ ?>