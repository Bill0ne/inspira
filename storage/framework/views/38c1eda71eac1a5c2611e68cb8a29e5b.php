<?php $__env->startSection('content'); ?>
    <?php if($showStart): ?>
        <?php echo Form::open(Arr::except($formOptions, ['template'])); ?>

    <?php endif; ?>

    <?php
        do_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, request(), $form->getModel());
    ?>

    <?php if($showFields && $form->hasMainFields()): ?>
        <div class="<?php echo e($form->getWrapperClass()); ?>">
            <?php echo e($form->getOpenWrapperFormColumns()); ?>


            <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($field->getName() == $form->getBreakFieldPoint()): ?>
                <?php break; ?>

            <?php else: ?>
                <?php unset($fields[$key]); ?>
            <?php endif; ?>

            <?php if(in_array($field->getName(), $exclude)) continue; ?>

            <?php echo $field->render(); ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php echo e($form->getCloseWrapperFormColumns()); ?>

    </div>
<?php endif; ?>

<?php if($showEnd): ?>
    <?php echo Form::close(); ?>

<?php endif; ?>

<?php echo $__env->yieldContent('form_end'); ?>
<?php $__env->stopSection(); ?>

<?php if($form->getValidatorClass()): ?>
<?php if($form->isUseInlineJs()): ?>
    <?php echo Assets::scriptToHtml('jquery'); ?>

    <?php echo Assets::scriptToHtml('form-validation'); ?>

    <?php echo $form->renderValidatorJs(); ?>

<?php else: ?>
    <?php $__env->startPush('footer'); ?>
        <?php echo $form->renderValidatorJs(); ?>

    <?php $__env->stopPush(); ?>
<?php endif; ?>
<?php endif; ?>

<?php echo $__env->make('core/acl::layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/core/acl/resources/views/auth/form.blade.php ENDPATH**/ ?>