<?php
    $field['options'] = BaseHelper::getFonts();
?>

<?php echo Form::customSelect(
    $name,
    ['' => __('-- Select --')] + array_combine($field['options'], $field['options']),
    $selected,
    ['data-bb-toggle' => 'google-font-selector'],
); ?>


<?php if (! $__env->hasRenderedOnce('7ea2252d-aee1-4f90-8fbc-f065c08f356d')): $__env->markAsRenderedOnce('7ea2252d-aee1-4f90-8fbc-f065c08f356d'); ?>
    <?php $__env->startPush('footer'); ?>
        <?php $__currentLoopData = array_chunk($field['options'], 200); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fonts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo Html::style(
                BaseHelper::getGoogleFontsURL() .
                    '/css?family=' .
                    implode('|', array_map('urlencode', array_filter($fonts))) .
                    '&display=swap',
            ); ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/core/base/resources/views/forms/partials/google-fonts.blade.php ENDPATH**/ ?>