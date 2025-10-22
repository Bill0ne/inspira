<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'id',
    'name',
    'logo',
    'url' => null,
    'description' => null,
    'status' => get_payment_setting('status', $id),
    'defaultDescriptionValue' => __('Payment with :paymentType', ['paymentType' => $name]),
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
    'id',
    'name',
    'logo',
    'url' => null,
    'description' => null,
    'status' => get_payment_setting('status', $id),
    'defaultDescriptionValue' => __('Payment with :paymentType', ['paymentType' => $name]),
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $id = $id ?? Str::slug($name);
?>

<?php if (isset($component)) { $__componentOriginalc107e2f90dff5eb05519f33918d2c807 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc107e2f90dff5eb05519f33918d2c807 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::card.index','data' => ['class' => 'mb-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mb-3']); ?>
    <?php if (isset($component)) { $__componentOriginal44c83e2eb600bf127a623cda69e3ac8b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal44c83e2eb600bf127a623cda69e3ac8b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::table.index','data' => ['hover' => false,'striped' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['hover' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'striped' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
        <?php if (isset($component)) { $__componentOriginal4d7e52336690b9ea120a6913f2c28a6b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4d7e52336690b9ea120a6913f2c28a6b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::table.body.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::table.body'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
            <?php if (isset($component)) { $__componentOriginal6776c17865a79e242405889703595892 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6776c17865a79e242405889703595892 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::table.body.row','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::table.body.row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                <?php if (isset($component)) { $__componentOriginal39a228eaec73c356bdf14858f816ca38 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal39a228eaec73c356bdf14858f816ca38 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::table.body.cell','data' => ['class' => 'border-end','style' => 'width: 5%']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::table.body.cell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'border-end','style' => 'width: 5%']); ?>
                    <?php if (isset($component)) { $__componentOriginal73995948b3bd877b76251b40caf28170 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal73995948b3bd877b76251b40caf28170 = $attributes; } ?>
<?php $component = Botble\Icon\View\Components\Icon::resolve(['name' => 'ti ti-wallet'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Botble\Icon\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal73995948b3bd877b76251b40caf28170)): ?>
<?php $attributes = $__attributesOriginal73995948b3bd877b76251b40caf28170; ?>
<?php unset($__attributesOriginal73995948b3bd877b76251b40caf28170); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal73995948b3bd877b76251b40caf28170)): ?>
<?php $component = $__componentOriginal73995948b3bd877b76251b40caf28170; ?>
<?php unset($__componentOriginal73995948b3bd877b76251b40caf28170); ?>
<?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal39a228eaec73c356bdf14858f816ca38)): ?>
<?php $attributes = $__attributesOriginal39a228eaec73c356bdf14858f816ca38; ?>
<?php unset($__attributesOriginal39a228eaec73c356bdf14858f816ca38); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal39a228eaec73c356bdf14858f816ca38)): ?>
<?php $component = $__componentOriginal39a228eaec73c356bdf14858f816ca38; ?>
<?php unset($__componentOriginal39a228eaec73c356bdf14858f816ca38); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal39a228eaec73c356bdf14858f816ca38 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal39a228eaec73c356bdf14858f816ca38 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::table.body.cell','data' => ['style' => 'width: 20%']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::table.body.cell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['style' => 'width: 20%']); ?>
                    <img src="<?php echo e($logo); ?>" alt="<?php echo e($name); ?>" style="width: 8rem">
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal39a228eaec73c356bdf14858f816ca38)): ?>
<?php $attributes = $__attributesOriginal39a228eaec73c356bdf14858f816ca38; ?>
<?php unset($__attributesOriginal39a228eaec73c356bdf14858f816ca38); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal39a228eaec73c356bdf14858f816ca38)): ?>
<?php $component = $__componentOriginal39a228eaec73c356bdf14858f816ca38; ?>
<?php unset($__componentOriginal39a228eaec73c356bdf14858f816ca38); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal39a228eaec73c356bdf14858f816ca38 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal39a228eaec73c356bdf14858f816ca38 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::table.body.cell','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::table.body.cell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                    <?php if($url): ?>
                        <a href="<?php echo e($url); ?>" target="_blank"><?php echo e($name); ?></a>
                    <?php else: ?>
                        <?php echo e($name); ?>

                    <?php endif; ?>
                    <?php if($description): ?>
                        <p class="mb-0"><?php echo e($description); ?></p>
                    <?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal39a228eaec73c356bdf14858f816ca38)): ?>
<?php $attributes = $__attributesOriginal39a228eaec73c356bdf14858f816ca38; ?>
<?php unset($__attributesOriginal39a228eaec73c356bdf14858f816ca38); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal39a228eaec73c356bdf14858f816ca38)): ?>
<?php $component = $__componentOriginal39a228eaec73c356bdf14858f816ca38; ?>
<?php unset($__componentOriginal39a228eaec73c356bdf14858f816ca38); ?>
<?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6776c17865a79e242405889703595892)): ?>
<?php $attributes = $__attributesOriginal6776c17865a79e242405889703595892; ?>
<?php unset($__attributesOriginal6776c17865a79e242405889703595892); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6776c17865a79e242405889703595892)): ?>
<?php $component = $__componentOriginal6776c17865a79e242405889703595892; ?>
<?php unset($__componentOriginal6776c17865a79e242405889703595892); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal6776c17865a79e242405889703595892 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6776c17865a79e242405889703595892 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::table.body.row','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::table.body.row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                <?php if (isset($component)) { $__componentOriginal39a228eaec73c356bdf14858f816ca38 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal39a228eaec73c356bdf14858f816ca38 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::table.body.cell','data' => ['colspan' => '3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::table.body.cell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['colspan' => '3']); ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['payment-name-label-group', 'hidden' => !$status]); ?>">
                                <?php echo e(trans('plugins/payment::payment.use')); ?>

                                <span class="method-name-label"><?php echo e(get_payment_setting('name', $id)); ?></span>
                            </div>
                        </div>

                        <?php if (isset($component)) { $__componentOriginal922f7d3260a518f4cf606eecf9669dcb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal922f7d3260a518f4cf606eecf9669dcb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::button','data' => ['class' => \Illuminate\Support\Arr::toCssClasses(['toggle-payment-item edit-payment-item-btn-trigger', 'hidden' => !$status])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssClasses(['toggle-payment-item edit-payment-item-btn-trigger', 'hidden' => !$status]))]); ?>
                            <?php echo e(trans('plugins/payment::payment.edit')); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal922f7d3260a518f4cf606eecf9669dcb)): ?>
<?php $attributes = $__attributesOriginal922f7d3260a518f4cf606eecf9669dcb; ?>
<?php unset($__attributesOriginal922f7d3260a518f4cf606eecf9669dcb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal922f7d3260a518f4cf606eecf9669dcb)): ?>
<?php $component = $__componentOriginal922f7d3260a518f4cf606eecf9669dcb; ?>
<?php unset($__componentOriginal922f7d3260a518f4cf606eecf9669dcb); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginal922f7d3260a518f4cf606eecf9669dcb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal922f7d3260a518f4cf606eecf9669dcb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::button','data' => ['class' => \Illuminate\Support\Arr::toCssClasses(['toggle-payment-item save-payment-item-btn-trigger', 'hidden' => $status])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssClasses(['toggle-payment-item save-payment-item-btn-trigger', 'hidden' => $status]))]); ?>
                            <?php echo e(trans('plugins/payment::payment.settings')); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal922f7d3260a518f4cf606eecf9669dcb)): ?>
<?php $attributes = $__attributesOriginal922f7d3260a518f4cf606eecf9669dcb; ?>
<?php unset($__attributesOriginal922f7d3260a518f4cf606eecf9669dcb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal922f7d3260a518f4cf606eecf9669dcb)): ?>
<?php $component = $__componentOriginal922f7d3260a518f4cf606eecf9669dcb; ?>
<?php unset($__componentOriginal922f7d3260a518f4cf606eecf9669dcb); ?>
<?php endif; ?>
                    </div>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal39a228eaec73c356bdf14858f816ca38)): ?>
<?php $attributes = $__attributesOriginal39a228eaec73c356bdf14858f816ca38; ?>
<?php unset($__attributesOriginal39a228eaec73c356bdf14858f816ca38); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal39a228eaec73c356bdf14858f816ca38)): ?>
<?php $component = $__componentOriginal39a228eaec73c356bdf14858f816ca38; ?>
<?php unset($__componentOriginal39a228eaec73c356bdf14858f816ca38); ?>
<?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6776c17865a79e242405889703595892)): ?>
<?php $attributes = $__attributesOriginal6776c17865a79e242405889703595892; ?>
<?php unset($__attributesOriginal6776c17865a79e242405889703595892); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6776c17865a79e242405889703595892)): ?>
<?php $component = $__componentOriginal6776c17865a79e242405889703595892; ?>
<?php unset($__componentOriginal6776c17865a79e242405889703595892); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal6776c17865a79e242405889703595892 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6776c17865a79e242405889703595892 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::table.body.row','data' => ['class' => 'payment-content-item hidden']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::table.body.row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'payment-content-item hidden']); ?>
                <?php if (isset($component)) { $__componentOriginal39a228eaec73c356bdf14858f816ca38 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal39a228eaec73c356bdf14858f816ca38 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::table.body.cell','data' => ['colspan' => '3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::table.body.cell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['colspan' => '3']); ?>
                    <?php if (isset($component)) { $__componentOriginald83dae5750a07af1a413e54a0071b325 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald83dae5750a07af1a413e54a0071b325 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::form.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                        <input type="hidden" name="type" value="<?php echo e($id); ?>" class="payment_type" />

                        <div class="row">
                            <div class="col-md-6">
                                <?php if (isset($component)) { $__componentOriginal50e5e771b30c35423d2b4f118feb7c0c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal50e5e771b30c35423d2b4f118feb7c0c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::form.label','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::form.label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                    <?php echo e(trans('plugins/payment::payment.configuration_instruction', ['name' => $name])); ?>

                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal50e5e771b30c35423d2b4f118feb7c0c)): ?>
<?php $attributes = $__attributesOriginal50e5e771b30c35423d2b4f118feb7c0c; ?>
<?php unset($__attributesOriginal50e5e771b30c35423d2b4f118feb7c0c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal50e5e771b30c35423d2b4f118feb7c0c)): ?>
<?php $component = $__componentOriginal50e5e771b30c35423d2b4f118feb7c0c; ?>
<?php unset($__componentOriginal50e5e771b30c35423d2b4f118feb7c0c); ?>
<?php endif; ?>

                                <p class="mb-2"><?php echo e(trans('plugins/payment::payment.configuration_requirement', ['name' => $name])); ?>:</p>

                                <?php echo e($instructions); ?>

                            </div>

                            <div class="col-md-6">
                                <?php if (isset($component)) { $__componentOriginala5b2ce8ea835a1a6ed10854da20fa051 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala5b2ce8ea835a1a6ed10854da20fa051 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::form.text-input','data' => ['label' => trans('plugins/payment::payment.method_name'),'name' => get_payment_setting_key('name', $id),'dataCounter' => '400','value' => get_payment_setting('name', $id, trans('plugins/payment::payment.pay_online_via', ['name' => $name]))]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::form.text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('plugins/payment::payment.method_name')),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(get_payment_setting_key('name', $id)),'data-counter' => '400','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(get_payment_setting('name', $id, trans('plugins/payment::payment.pay_online_via', ['name' => $name])))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala5b2ce8ea835a1a6ed10854da20fa051)): ?>
<?php $attributes = $__attributesOriginala5b2ce8ea835a1a6ed10854da20fa051; ?>
<?php unset($__attributesOriginala5b2ce8ea835a1a6ed10854da20fa051); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala5b2ce8ea835a1a6ed10854da20fa051)): ?>
<?php $component = $__componentOriginala5b2ce8ea835a1a6ed10854da20fa051; ?>
<?php unset($__componentOriginala5b2ce8ea835a1a6ed10854da20fa051); ?>
<?php endif; ?>

                                <?php if (isset($component)) { $__componentOriginal98af68e526ddc4187878063a3b54ba78 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal98af68e526ddc4187878063a3b54ba78 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::form.textarea','data' => ['label' => trans('core/base::forms.description'),'name' => get_payment_setting_key('description', $id),'value' => get_payment_setting('description', $id, $defaultDescriptionValue)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::form.textarea'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('core/base::forms.description')),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(get_payment_setting_key('description', $id)),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(get_payment_setting('description', $id, $defaultDescriptionValue))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal98af68e526ddc4187878063a3b54ba78)): ?>
<?php $attributes = $__attributesOriginal98af68e526ddc4187878063a3b54ba78; ?>
<?php unset($__attributesOriginal98af68e526ddc4187878063a3b54ba78); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal98af68e526ddc4187878063a3b54ba78)): ?>
<?php $component = $__componentOriginal98af68e526ddc4187878063a3b54ba78; ?>
<?php unset($__componentOriginal98af68e526ddc4187878063a3b54ba78); ?>
<?php endif; ?>

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
                                    <?php if (isset($component)) { $__componentOriginal50e5e771b30c35423d2b4f118feb7c0c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal50e5e771b30c35423d2b4f118feb7c0c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::form.label','data' => ['for' => ''.e($logoKey = get_payment_setting_key('logo', $id)).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::form.label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => ''.e($logoKey = get_payment_setting_key('logo', $id)).'']); ?>
                                        <?php echo e(trans('plugins/payment::payment.method_logo')); ?>

                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal50e5e771b30c35423d2b4f118feb7c0c)): ?>
<?php $attributes = $__attributesOriginal50e5e771b30c35423d2b4f118feb7c0c; ?>
<?php unset($__attributesOriginal50e5e771b30c35423d2b4f118feb7c0c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal50e5e771b30c35423d2b4f118feb7c0c)): ?>
<?php $component = $__componentOriginal50e5e771b30c35423d2b4f118feb7c0c; ?>
<?php unset($__componentOriginal50e5e771b30c35423d2b4f118feb7c0c); ?>
<?php endif; ?>
                                    <?php echo e(Form::mediaImage($logoKey, get_payment_setting('logo', $id))); ?>

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

                                <?php if (isset($component)) { $__componentOriginal20d878510d8f6b63da7004efc7cea55f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal20d878510d8f6b63da7004efc7cea55f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::form.fieldset','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::form.fieldset'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                    <legend class="fs-4 fw-semibold mb-3">
                                        <?php echo e(trans('plugins/payment::payment.please_provide_information')); ?>

                                        <a href="<?php echo e($url); ?>" target="_blank"><?php echo e($name); ?></a>:
                                    </legend>

                                    <?php echo e($fields); ?>


                                    <?php echo apply_filters(PAYMENT_METHOD_SETTINGS_CONTENT, null, $id); ?>

                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal20d878510d8f6b63da7004efc7cea55f)): ?>
<?php $attributes = $__attributesOriginal20d878510d8f6b63da7004efc7cea55f; ?>
<?php unset($__attributesOriginal20d878510d8f6b63da7004efc7cea55f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal20d878510d8f6b63da7004efc7cea55f)): ?>
<?php $component = $__componentOriginal20d878510d8f6b63da7004efc7cea55f; ?>
<?php unset($__componentOriginal20d878510d8f6b63da7004efc7cea55f); ?>
<?php endif; ?>
                            </div>
                        </div>

                        <div class="btn-list justify-content-end">
                            <?php if (isset($component)) { $__componentOriginal922f7d3260a518f4cf606eecf9669dcb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal922f7d3260a518f4cf606eecf9669dcb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::button','data' => ['type' => 'button','class' => \Illuminate\Support\Arr::toCssClasses(['disable-payment-item', 'hidden' => !$status])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssClasses(['disable-payment-item', 'hidden' => !$status]))]); ?>
                                <?php echo e(trans('plugins/payment::payment.deactivate')); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal922f7d3260a518f4cf606eecf9669dcb)): ?>
<?php $attributes = $__attributesOriginal922f7d3260a518f4cf606eecf9669dcb; ?>
<?php unset($__attributesOriginal922f7d3260a518f4cf606eecf9669dcb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal922f7d3260a518f4cf606eecf9669dcb)): ?>
<?php $component = $__componentOriginal922f7d3260a518f4cf606eecf9669dcb; ?>
<?php unset($__componentOriginal922f7d3260a518f4cf606eecf9669dcb); ?>
<?php endif; ?>

                            <?php if (isset($component)) { $__componentOriginal922f7d3260a518f4cf606eecf9669dcb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal922f7d3260a518f4cf606eecf9669dcb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::button','data' => ['class' => \Illuminate\Support\Arr::toCssClasses(['save-payment-item btn-text-trigger-save', 'hidden' => $status]),'type' => 'button','color' => 'info']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssClasses(['save-payment-item btn-text-trigger-save', 'hidden' => $status])),'type' => 'button','color' => 'info']); ?>
                                <?php echo e(trans('plugins/payment::payment.activate')); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal922f7d3260a518f4cf606eecf9669dcb)): ?>
<?php $attributes = $__attributesOriginal922f7d3260a518f4cf606eecf9669dcb; ?>
<?php unset($__attributesOriginal922f7d3260a518f4cf606eecf9669dcb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal922f7d3260a518f4cf606eecf9669dcb)): ?>
<?php $component = $__componentOriginal922f7d3260a518f4cf606eecf9669dcb; ?>
<?php unset($__componentOriginal922f7d3260a518f4cf606eecf9669dcb); ?>
<?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginal922f7d3260a518f4cf606eecf9669dcb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal922f7d3260a518f4cf606eecf9669dcb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::button','data' => ['type' => 'button','color' => 'info','class' => \Illuminate\Support\Arr::toCssClasses(['save-payment-item btn-text-trigger-update', 'hidden' => !$status])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','color' => 'info','class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssClasses(['save-payment-item btn-text-trigger-update', 'hidden' => !$status]))]); ?>
                                <?php echo e(trans('plugins/payment::payment.update')); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal922f7d3260a518f4cf606eecf9669dcb)): ?>
<?php $attributes = $__attributesOriginal922f7d3260a518f4cf606eecf9669dcb; ?>
<?php unset($__attributesOriginal922f7d3260a518f4cf606eecf9669dcb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal922f7d3260a518f4cf606eecf9669dcb)): ?>
<?php $component = $__componentOriginal922f7d3260a518f4cf606eecf9669dcb; ?>
<?php unset($__componentOriginal922f7d3260a518f4cf606eecf9669dcb); ?>
<?php endif; ?>
                        </div>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald83dae5750a07af1a413e54a0071b325)): ?>
<?php $attributes = $__attributesOriginald83dae5750a07af1a413e54a0071b325; ?>
<?php unset($__attributesOriginald83dae5750a07af1a413e54a0071b325); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald83dae5750a07af1a413e54a0071b325)): ?>
<?php $component = $__componentOriginald83dae5750a07af1a413e54a0071b325; ?>
<?php unset($__componentOriginald83dae5750a07af1a413e54a0071b325); ?>
<?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal39a228eaec73c356bdf14858f816ca38)): ?>
<?php $attributes = $__attributesOriginal39a228eaec73c356bdf14858f816ca38; ?>
<?php unset($__attributesOriginal39a228eaec73c356bdf14858f816ca38); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal39a228eaec73c356bdf14858f816ca38)): ?>
<?php $component = $__componentOriginal39a228eaec73c356bdf14858f816ca38; ?>
<?php unset($__componentOriginal39a228eaec73c356bdf14858f816ca38); ?>
<?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6776c17865a79e242405889703595892)): ?>
<?php $attributes = $__attributesOriginal6776c17865a79e242405889703595892; ?>
<?php unset($__attributesOriginal6776c17865a79e242405889703595892); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6776c17865a79e242405889703595892)): ?>
<?php $component = $__componentOriginal6776c17865a79e242405889703595892; ?>
<?php unset($__componentOriginal6776c17865a79e242405889703595892); ?>
<?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4d7e52336690b9ea120a6913f2c28a6b)): ?>
<?php $attributes = $__attributesOriginal4d7e52336690b9ea120a6913f2c28a6b; ?>
<?php unset($__attributesOriginal4d7e52336690b9ea120a6913f2c28a6b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4d7e52336690b9ea120a6913f2c28a6b)): ?>
<?php $component = $__componentOriginal4d7e52336690b9ea120a6913f2c28a6b; ?>
<?php unset($__componentOriginal4d7e52336690b9ea120a6913f2c28a6b); ?>
<?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal44c83e2eb600bf127a623cda69e3ac8b)): ?>
<?php $attributes = $__attributesOriginal44c83e2eb600bf127a623cda69e3ac8b; ?>
<?php unset($__attributesOriginal44c83e2eb600bf127a623cda69e3ac8b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal44c83e2eb600bf127a623cda69e3ac8b)): ?>
<?php $component = $__componentOriginal44c83e2eb600bf127a623cda69e3ac8b; ?>
<?php unset($__componentOriginal44c83e2eb600bf127a623cda69e3ac8b); ?>
<?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc107e2f90dff5eb05519f33918d2c807)): ?>
<?php $attributes = $__attributesOriginalc107e2f90dff5eb05519f33918d2c807; ?>
<?php unset($__attributesOriginalc107e2f90dff5eb05519f33918d2c807); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc107e2f90dff5eb05519f33918d2c807)): ?>
<?php $component = $__componentOriginalc107e2f90dff5eb05519f33918d2c807; ?>
<?php unset($__componentOriginalc107e2f90dff5eb05519f33918d2c807); ?>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/plugins/payment/resources/views/components/settings-card.blade.php ENDPATH**/ ?>