<?php if($showFields): ?>
    <?php echo e($form->getOpenWrapperFormColumns()); ?>


    <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(in_array($field->getName(), $exclude)) continue; ?>

        <?php echo $field->render(); ?>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php echo e($form->getCloseWrapperFormColumns()); ?>

<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/plugins/payment/resources/views/forms/fields-only.blade.php ENDPATH**/ ?>