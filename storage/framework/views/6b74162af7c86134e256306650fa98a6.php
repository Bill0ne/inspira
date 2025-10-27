<?php if($showStart): ?>
    <?php echo Form::open(Arr::except($formOptions, ['template'])); ?>

<?php endif; ?>

<?php
    do_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, request(), $form->getModel());
?>

<?php if($showFields): ?>
    <?php echo e($form->getOpenWrapperFormColumns()); ?>


    <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(in_array($field->getName(), $exclude)) continue; ?>

        <?php echo $field->render(); ?>

        <?php if(defined('BASE_FILTER_SLUG_AREA') && $field->getName() == SlugHelper::getColumnNameToGenerateSlug($form->getModel())): ?>
            <?php echo apply_filters(BASE_FILTER_SLUG_AREA, null, $form->getModel()); ?>

        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php echo e($form->getCloseWrapperFormColumns()); ?>

<?php endif; ?>

<?php $__currentLoopData = $form->getMetaBoxes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $metaBox): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php echo $form->getMetaBox($key); ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php do_action(BASE_ACTION_META_BOXES, 'top', $form->getModel()) ?>
<?php do_action(BASE_ACTION_META_BOXES, 'side', $form->getModel()) ?>
<?php do_action(BASE_ACTION_META_BOXES, 'advanced', $form->getModel()) ?>

<?php echo $form->getActionButtons(); ?>


<?php if($showEnd): ?>
    <?php echo Form::close(); ?>

<?php endif; ?>

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
<?php /**PATH D:\xampp\htdocs\inspira\platform/core/base/resources/views/forms/form-no-wrap.blade.php ENDPATH**/ ?>