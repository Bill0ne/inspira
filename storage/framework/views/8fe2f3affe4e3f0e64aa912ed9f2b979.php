<?php
    $manageLicense = auth()
        ->user()
        ->hasPermission('core.manage.license');
?>

<?php if (isset($component)) { $__componentOriginalecda78b9fe8916cbd83b85e55a8b7a1c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalecda78b9fe8916cbd83b85e55a8b7a1c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::alert','data' => ['type' => 'warning','important' => true,'class' => \Illuminate\Support\Arr::toCssClasses(['alert-license alert-sticky small bg-warning text-white', 'vertical-wrapper' => AdminAppearance::isVerticalLayout()]),'icon' => '','style' => \Illuminate\Support\Arr::toCssStyles(['display: none' => $hidden ?? true]),'dataBbToggle' => 'authorized-reminder']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'warning','important' => true,'class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssClasses(['alert-license alert-sticky small bg-warning text-white', 'vertical-wrapper' => AdminAppearance::isVerticalLayout()])),'icon' => '','style' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssStyles(['display: none' => $hidden ?? true])),'data-bb-toggle' => 'authorized-reminder']); ?>
    <div class="<?php echo e(AdminAppearance::getContainerWidth()); ?>">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                Your license is invalid, please contact support. If you didn't set up license code, please go to
                <a
                    href="<?php echo e(route('settings.general')); ?>"
                    class="text-white fw-bold"
                > Settings </a> to activate license!
            </div>

            <?php if($manageLicense): ?>
                <a
                    class="btn-close"
                    data-bs-toggle="modal"
                    data-bs-target="#quick-activation-license-modal"
                    aria-label="close"
                ></a>
            <?php endif; ?>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalecda78b9fe8916cbd83b85e55a8b7a1c)): ?>
<?php $attributes = $__attributesOriginalecda78b9fe8916cbd83b85e55a8b7a1c; ?>
<?php unset($__attributesOriginalecda78b9fe8916cbd83b85e55a8b7a1c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalecda78b9fe8916cbd83b85e55a8b7a1c)): ?>
<?php $component = $__componentOriginalecda78b9fe8916cbd83b85e55a8b7a1c; ?>
<?php unset($__componentOriginalecda78b9fe8916cbd83b85e55a8b7a1c); ?>
<?php endif; ?>

<?php if($manageLicense): ?>
    <?php echo $__env->make('core/base::system.partials.license-activation-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\inspira\platform/core/base/resources/views/system/license-invalid.blade.php ENDPATH**/ ?>