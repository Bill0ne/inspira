<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['name', 'allowThumb' => true, 'images' => [], 'addImagesLabel' => trans('core/base::forms.add_images'), 'resetLabel' => trans('core/base::forms.reset')]));

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

foreach (array_filter((['name', 'allowThumb' => true, 'images' => [], 'addImagesLabel' => trans('core/base::forms.add_images'), 'resetLabel' => trans('core/base::forms.reset')]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div <?php echo e($attributes->merge(['class' => 'gallery-images-wrapper list-images form-fieldset'])); ?>>
    <div class="images-wrapper mb-2">
        <div
            data-bb-toggle="gallery-add"
            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'text-center cursor-pointer default-placeholder-gallery-image',
                'hidden' => !empty($images),
            ]); ?>"
            data-name="<?php echo e($name); ?>"
        >
            <div class="mb-3">
                <?php if (isset($component)) { $__componentOriginal73995948b3bd877b76251b40caf28170 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal73995948b3bd877b76251b40caf28170 = $attributes; } ?>
<?php $component = Botble\Icon\View\Components\Icon::resolve(['name' => 'ti ti-photo-plus','size' => 'md'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Botble\Icon\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-secondary']); ?>
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
            </div>
            <p class="mb-0 text-body">
                <?php echo e(trans('core/base::base.click_here')); ?>

                <?php echo e(trans('core/base::base.to_add_more_image')); ?>.
            </p>
        </div>
        <input
            name="<?php echo e($name); ?>"
            type="hidden"
        >
        <div
            class="row w-100 list-gallery-media-images <?php if(empty($images)): ?> hidden <?php endif; ?>"
            data-name="<?php echo e($name); ?>"
            data-allow-thumb="<?php echo e($allowThumb); ?>"
        >
            <?php if(!empty($images)): ?>
                <?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(!empty($image)): ?>
                        <div class="col-lg-2 col-md-3 col-4 gallery-image-item-handler mb-2">
                            <div class="custom-image-box image-box">
                                <input
                                    class="image-data"
                                    name="<?php echo e($name); ?>"
                                    type="hidden"
                                    value="<?php echo e($image); ?>"
                                >
                                <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                    'preview-image-wrapper w-100 mb-1',
                                    'preview-image-wrapper-not-allow-thumb' => !($allowThumb = Arr::get(
                                        $attributes,
                                        'allow_thumb',
                                        true)),
                                ]); ?>">
                                    <div class="preview-image-inner">
                                        <?php if (isset($component)) { $__componentOriginalb52fd548b9021b0206841fc657a1fbc9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb52fd548b9021b0206841fc657a1fbc9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::image','data' => ['class' => 'preview-image','dataDefault' => ''.e($defaultImage = RvMedia::getDefaultImage()).'','src' => ''.e(RvMedia::getImageUrl($image, $allowThumb ? 'thumb' : null, false, $defaultImage)).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::image'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'preview-image','data-default' => ''.e($defaultImage = RvMedia::getDefaultImage()).'','src' => ''.e(RvMedia::getImageUrl($image, $allowThumb ? 'thumb' : null, false, $defaultImage)).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb52fd548b9021b0206841fc657a1fbc9)): ?>
<?php $attributes = $__attributesOriginalb52fd548b9021b0206841fc657a1fbc9; ?>
<?php unset($__attributesOriginalb52fd548b9021b0206841fc657a1fbc9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb52fd548b9021b0206841fc657a1fbc9)): ?>
<?php $component = $__componentOriginalb52fd548b9021b0206841fc657a1fbc9; ?>
<?php unset($__componentOriginalb52fd548b9021b0206841fc657a1fbc9); ?>
<?php endif; ?>
                                        <div class="image-picker-backdrop"></div>

                                        <span class="image-picker-remove-button">
                                            <?php if (isset($component)) { $__componentOriginal922f7d3260a518f4cf606eecf9669dcb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal922f7d3260a518f4cf606eecf9669dcb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::button','data' => ['class' => 'p-0','style' => \Illuminate\Support\Arr::toCssStyles(['display: none' => empty($image)]),'pill' => true,'dataBbToggle' => 'image-picker-remove','size' => 'sm','icon' => 'ti ti-x','iconOnly' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'p-0','style' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssStyles(['display: none' => empty($image)])),'pill' => true,'data-bb-toggle' => 'image-picker-remove','size' => 'sm','icon' => 'ti ti-x','icon-only' => true]); ?>
                                                <?php echo e(__('Remove image')); ?>

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
                                        </span>
                                        <div
                                            data-bb-toggle="image-picker-edit"
                                            class="image-box-actions cursor-pointer"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
    </div>
    <div
        style="<?php echo \Illuminate\Support\Arr::toCssStyles(['display: none' => empty($image)]) ?>"
        class="footer-action"
    >
        <a
            data-bb-toggle="gallery-add"
            href="#"
            class="me-2 cursor-pointer"
        ><?php echo e($addImagesLabel); ?></a>
        <a
            href="#"
            class="text-danger"
            data-bb-toggle="gallery-reset"
        >
            <?php echo e($resetLabel); ?>

        </a>
    </div>
</div>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/core/base/resources/views/components/form/images.blade.php ENDPATH**/ ?>