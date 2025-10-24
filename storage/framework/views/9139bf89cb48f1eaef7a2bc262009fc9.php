<?php if (isset($component)) { $__componentOriginal689b5f1c2d5eb9b484460a3d32ffcf95 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal689b5f1c2d5eb9b484460a3d32ffcf95 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'fa081de1c3ee47622336b4eeafa01705::settings-card','data' => ['name' => Arr::get($formOptions, 'payment_name'),'id' => Arr::get($formOptions, 'payment_id'),'logo' => Arr::get($formOptions, 'payment_logo'),'url' => Arr::get($formOptions, 'payment_url'),'description' => Arr::get($formOptions, 'payment_description'),'defaultDescriptionValue' => Arr::get($formOptions, 'default_description_value')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('plugins-payment::settings-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(Arr::get($formOptions, 'payment_name')),'id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(Arr::get($formOptions, 'payment_id')),'logo' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(Arr::get($formOptions, 'payment_logo')),'url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(Arr::get($formOptions, 'payment_url')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(Arr::get($formOptions, 'payment_description')),'default-description-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(Arr::get($formOptions, 'default_description_value'))]); ?>
     <?php $__env->slot('instructions', null, []); ?> 
        <?php echo e($form->getPaymentInstructions()); ?>

     <?php $__env->endSlot(); ?>

    <?php if($showFields): ?>
         <?php $__env->slot('fields', null, []); ?> 
            <?php echo e($form->getOpenWrapperFormColumns()); ?>


            <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(in_array($field->getName(), $exclude)) continue; ?>

                <?php echo $field->render(); ?>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php echo e($form->getCloseWrapperFormColumns()); ?>

         <?php $__env->endSlot(); ?>
    <?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal689b5f1c2d5eb9b484460a3d32ffcf95)): ?>
<?php $attributes = $__attributesOriginal689b5f1c2d5eb9b484460a3d32ffcf95; ?>
<?php unset($__attributesOriginal689b5f1c2d5eb9b484460a3d32ffcf95); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal689b5f1c2d5eb9b484460a3d32ffcf95)): ?>
<?php $component = $__componentOriginal689b5f1c2d5eb9b484460a3d32ffcf95; ?>
<?php unset($__componentOriginal689b5f1c2d5eb9b484460a3d32ffcf95); ?>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\inspira\platform/plugins/payment/resources/views/forms/payment-method.blade.php ENDPATH**/ ?>