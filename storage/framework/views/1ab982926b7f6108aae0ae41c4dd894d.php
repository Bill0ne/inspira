<?php if (isset($component)) { $__componentOriginaldc8ac54b6bf7eb0d0560fdd5aa630687 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldc8ac54b6bf7eb0d0560fdd5aa630687 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::modal','data' => ['id' => 'quick-activation-license-modal','title' => __('License Activation')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'quick-activation-license-modal','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('License Activation'))]); ?>
    <form
        action="<?php echo e(route('settings.license.activate')); ?>"
        method="POST"
        data-bb-toggle="activate-license"
        data-reload="true"
    >
        <?php if (isset($component)) { $__componentOriginald34a25f8ff9d26735e5b798f732d2b5d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald34a25f8ff9d26735e5b798f732d2b5d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::license.form','data' => ['reset' => false,'isVue' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::license.form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['reset' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'is-vue' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald34a25f8ff9d26735e5b798f732d2b5d)): ?>
<?php $attributes = $__attributesOriginald34a25f8ff9d26735e5b798f732d2b5d; ?>
<?php unset($__attributesOriginald34a25f8ff9d26735e5b798f732d2b5d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald34a25f8ff9d26735e5b798f732d2b5d)): ?>
<?php $component = $__componentOriginald34a25f8ff9d26735e5b798f732d2b5d; ?>
<?php unset($__componentOriginald34a25f8ff9d26735e5b798f732d2b5d); ?>
<?php endif; ?>
    </form>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldc8ac54b6bf7eb0d0560fdd5aa630687)): ?>
<?php $attributes = $__attributesOriginaldc8ac54b6bf7eb0d0560fdd5aa630687; ?>
<?php unset($__attributesOriginaldc8ac54b6bf7eb0d0560fdd5aa630687); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldc8ac54b6bf7eb0d0560fdd5aa630687)): ?>
<?php $component = $__componentOriginaldc8ac54b6bf7eb0d0560fdd5aa630687; ?>
<?php unset($__componentOriginaldc8ac54b6bf7eb0d0560fdd5aa630687); ?>
<?php endif; ?>

<?php if(Request::ajax()): ?>
    <script src="<?php echo e(asset('vendor/core/core/base/js/license-activation.js')); ?>"></script>
<?php else: ?>
    <?php $__env->startPush('footer'); ?>
        <script src="<?php echo e(asset('vendor/core/core/base/js/license-activation.js')); ?>"></script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/core/base/resources/views/system/partials/license-activation-modal.blade.php ENDPATH**/ ?>