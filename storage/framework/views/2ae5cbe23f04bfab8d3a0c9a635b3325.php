<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variables' => [],
    'functions' => [],
    'value' => null,
    'name' => null,
    'mode' => 'html',
    'helperText' => null,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'variables' => [],
    'functions' => [],
    'value' => null,
    'name' => null,
    'mode' => 'html',
    'helperText' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div class="twig-template">
    <div class="mb-3 btn-list">
        <?php if(! empty($variables)): ?>
            <?php if (isset($component)) { $__componentOriginalf8303636a16ac3e808e27fabe59149a5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8303636a16ac3e808e27fabe59149a5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::dropdown.index','data' => ['label' => __('Variables'),'icon' => 'ti ti-code']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Variables')),'icon' => 'ti ti-code']); ?>
                <?php $__currentLoopData = $variables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if (isset($component)) { $__componentOriginal7681c9e8cd9d4250104639dd6412633f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7681c9e8cd9d4250104639dd6412633f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::dropdown.item','data' => ['dataBbToggle' => 'twig-variable','dataKey' => $key]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::dropdown.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['data-bb-toggle' => 'twig-variable','data-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($key)]); ?>
                        <span class="text-danger"><?php echo e($key); ?></span>: <?php echo e(trans($label)); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7681c9e8cd9d4250104639dd6412633f)): ?>
<?php $attributes = $__attributesOriginal7681c9e8cd9d4250104639dd6412633f; ?>
<?php unset($__attributesOriginal7681c9e8cd9d4250104639dd6412633f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7681c9e8cd9d4250104639dd6412633f)): ?>
<?php $component = $__componentOriginal7681c9e8cd9d4250104639dd6412633f; ?>
<?php unset($__componentOriginal7681c9e8cd9d4250104639dd6412633f); ?>
<?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8303636a16ac3e808e27fabe59149a5)): ?>
<?php $attributes = $__attributesOriginalf8303636a16ac3e808e27fabe59149a5; ?>
<?php unset($__attributesOriginalf8303636a16ac3e808e27fabe59149a5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8303636a16ac3e808e27fabe59149a5)): ?>
<?php $component = $__componentOriginalf8303636a16ac3e808e27fabe59149a5; ?>
<?php unset($__componentOriginalf8303636a16ac3e808e27fabe59149a5); ?>
<?php endif; ?>
        <?php endif; ?>

        <?php if(! empty($functions)): ?>
            <?php if (isset($component)) { $__componentOriginalf8303636a16ac3e808e27fabe59149a5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8303636a16ac3e808e27fabe59149a5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::dropdown.index','data' => ['label' => __('Functions'),'icon' => 'ti ti-code']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Functions')),'icon' => 'ti ti-code']); ?>
                <?php $__currentLoopData = $functions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $function): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if (isset($component)) { $__componentOriginal7681c9e8cd9d4250104639dd6412633f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7681c9e8cd9d4250104639dd6412633f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::dropdown.item','data' => ['dataBbToggle' => 'twig-function','dataKey' => $key,'dataSample' => $function['sample']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::dropdown.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['data-bb-toggle' => 'twig-function','data-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($key),'data-sample' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($function['sample'])]); ?>
                        <span class="text-danger"><?php echo e($key); ?></span>: <?php echo e(trans($function['label'])); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7681c9e8cd9d4250104639dd6412633f)): ?>
<?php $attributes = $__attributesOriginal7681c9e8cd9d4250104639dd6412633f; ?>
<?php unset($__attributesOriginal7681c9e8cd9d4250104639dd6412633f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7681c9e8cd9d4250104639dd6412633f)): ?>
<?php $component = $__componentOriginal7681c9e8cd9d4250104639dd6412633f; ?>
<?php unset($__componentOriginal7681c9e8cd9d4250104639dd6412633f); ?>
<?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8303636a16ac3e808e27fabe59149a5)): ?>
<?php $attributes = $__attributesOriginalf8303636a16ac3e808e27fabe59149a5; ?>
<?php unset($__attributesOriginalf8303636a16ac3e808e27fabe59149a5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8303636a16ac3e808e27fabe59149a5)): ?>
<?php $component = $__componentOriginalf8303636a16ac3e808e27fabe59149a5; ?>
<?php unset($__componentOriginalf8303636a16ac3e808e27fabe59149a5); ?>
<?php endif; ?>
        <?php endif; ?>
    </div>

    <?php if (isset($component)) { $__componentOriginala0a922bb70d8e2bee74cdab0a323562a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala0a922bb70d8e2bee74cdab0a323562a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::form-group','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::form-group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <?php if (isset($component)) { $__componentOriginal55fef500914e635eef22b52cddd65ca8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal55fef500914e635eef22b52cddd65ca8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::form.code-editor','data' => ['name' => $name,'value' => $value,'mode' => $mode]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::form.code-editor'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($name),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($value),'mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mode)]); ?>
             <?php $__env->slot('helperText', null, []); ?> 
                <?php if($helperText): ?>
                    <?php echo e($helperText); ?>

                <?php else: ?>
                    <?php echo BaseHelper::clean(
                        __('Learn more about Twig template: :url', [
                            'url' => Html::link('https://twig.symfony.com/doc/3.x/', null, ['target' => '_blank']),
                        ]),
                    ); ?>

                <?php endif; ?>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal55fef500914e635eef22b52cddd65ca8)): ?>
<?php $attributes = $__attributesOriginal55fef500914e635eef22b52cddd65ca8; ?>
<?php unset($__attributesOriginal55fef500914e635eef22b52cddd65ca8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal55fef500914e635eef22b52cddd65ca8)): ?>
<?php $component = $__componentOriginal55fef500914e635eef22b52cddd65ca8; ?>
<?php unset($__componentOriginal55fef500914e635eef22b52cddd65ca8); ?>
<?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala0a922bb70d8e2bee74cdab0a323562a)): ?>
<?php $attributes = $__attributesOriginala0a922bb70d8e2bee74cdab0a323562a; ?>
<?php unset($__attributesOriginala0a922bb70d8e2bee74cdab0a323562a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala0a922bb70d8e2bee74cdab0a323562a)): ?>
<?php $component = $__componentOriginala0a922bb70d8e2bee74cdab0a323562a; ?>
<?php unset($__componentOriginala0a922bb70d8e2bee74cdab0a323562a); ?>
<?php endif; ?>
</div>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/core/base/resources/views/components/twig-editor.blade.php ENDPATH**/ ?>