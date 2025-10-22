<?php if (isset($component)) { $__componentOriginal98abe6467e067b33cedd28cf82bdc43d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal98abe6467e067b33cedd28cf82bdc43d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::tab.pane','data' => ['id' => $id]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::tab.pane'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($id)]); ?>
    <?php echo $content; ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal98abe6467e067b33cedd28cf82bdc43d)): ?>
<?php $attributes = $__attributesOriginal98abe6467e067b33cedd28cf82bdc43d; ?>
<?php unset($__attributesOriginal98abe6467e067b33cedd28cf82bdc43d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal98abe6467e067b33cedd28cf82bdc43d)): ?>
<?php $component = $__componentOriginal98abe6467e067b33cedd28cf82bdc43d; ?>
<?php unset($__componentOriginal98abe6467e067b33cedd28cf82bdc43d); ?>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/core/base/resources/views/forms/tabs/tab-content.blade.php ENDPATH**/ ?>