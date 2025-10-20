<?php if (isset($component)) { $__componentOriginal267ae10e99f5147c684b59e06e741a86 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal267ae10e99f5147c684b59e06e741a86 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::layouts.base','data' => ['bodyAttributes' => ['data-bs-theme' => 'dark']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::layouts.base'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['body-attributes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['data-bs-theme' => 'dark'])]); ?>
    <div class="row g-0 flex-fill vh-100">
        <div class="col-12 col-lg-6 col-xl-4 border-top-wide border-primary d-flex flex-column justify-content-center">
            <div class="container container-tight my-5 px-lg-5">
                <div class="text-center mb-4">
                    <?php echo $__env->make('core/base::partials.logo', ['defaultLogoHeight' => 50], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>
        <div class="position-relative col-12 col-lg-6 col-xl-8 d-none d-lg-block">
            <div
                class="bg-cover bg-white h-100 min-vh-100"
                style="background-image: url(<?php echo e($backgroundUrl); ?>)"
            ></div>
            <div class="end-0 bottom-0 position-absolute">
                <div class="text-white me-5 mb-4">
                    <h1 class="mb-1"><?php echo e(setting('admin_title', config('core.base.general.base_name'))); ?></h1>
                    <p><?php echo $__env->make('core/base::partials.copyright', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></p>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal267ae10e99f5147c684b59e06e741a86)): ?>
<?php $attributes = $__attributesOriginal267ae10e99f5147c684b59e06e741a86; ?>
<?php unset($__attributesOriginal267ae10e99f5147c684b59e06e741a86); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal267ae10e99f5147c684b59e06e741a86)): ?>
<?php $component = $__componentOriginal267ae10e99f5147c684b59e06e741a86; ?>
<?php unset($__componentOriginal267ae10e99f5147c684b59e06e741a86); ?>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/core/acl/resources/views/layouts/guest.blade.php ENDPATH**/ ?>