<?php
    Assets::addScriptsDirectly('vendor/core/core/base/js/repeater-field.js');

    $values = array_values(is_array($value) ? $value : (array) json_decode($value ?: '[]', true));

    $added = [];

    if (! empty($values)) {
        for ($index = 0; $index < count($values); $index++) {
            $group = '';

            foreach ($fields as $key => $field) {
                $group .= view('core/base::forms.partials.repeater-item', compact('name', 'index', 'key', 'field', 'values'));
            }

            $added[] = view('core/base::forms.partials.repeater-group', compact('group'));
        }
    }

    $group = '';

    foreach ($fields as $key => $field) {
        $group .= view('core/base::forms.partials.repeater-item', [
            'name' => $name,
            'index' => '__key__',
            'key' => $key,
            'field' => $field,
            'values' => [],
        ]);
    }

    $defaultFields = [view('core/base::forms.partials.repeater-group', compact('group'))->render()];

    $repeaterId = 'repeater_field_' . md5($name) . uniqid('_');
?>

<input
    name="<?php echo e($name); ?>"
    type="hidden"
    value="[]"
>

<div
    class="repeater-group"
    id="<?php echo e($repeaterId); ?>_group"
    data-next-index="<?php echo e(count($added)); ?>"
>
    <?php $__currentLoopData = $added; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <fieldset
            class="form-fieldset position-relative"
            data-id="<?php echo e($repeaterId); ?>_<?php echo e($loop->index); ?>"
            data-index="<?php echo e($loop->index); ?>"
        >
            <legend class="d-flex justify-content-end align-items-center">
                <?php if (isset($component)) { $__componentOriginal922f7d3260a518f4cf606eecf9669dcb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal922f7d3260a518f4cf606eecf9669dcb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::button','data' => ['class' => 'position-absolute remove-item-button','dataTarget' => 'repeater-remove','dataId' => ''.e($repeaterId).'_'.e($loop->index).'','icon' => 'ti ti-x','iconOnly' => true,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'position-absolute remove-item-button','data-target' => 'repeater-remove','data-id' => ''.e($repeaterId).'_'.e($loop->index).'','icon' => 'ti ti-x','icon-only' => true,'size' => 'sm']); ?>
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
            </legend>

            <div><?php echo $field; ?></div>
        </fieldset>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="mb-3">
    <?php if (isset($component)) { $__componentOriginal922f7d3260a518f4cf606eecf9669dcb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal922f7d3260a518f4cf606eecf9669dcb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::button','data' => ['dataTarget' => 'repeater-add','dataId' => ''.e($repeaterId).'','type' => 'button']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['data-target' => 'repeater-add','data-id' => ''.e($repeaterId).'','type' => 'button']); ?>
        <?php echo e(__('Add new')); ?>

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

<?php if(request()->ajax()): ?>
    <?php echo $__env->make('core/base::forms.partials.repeater-template', compact('repeaterId', 'defaultFields'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php else: ?>
    <?php $__env->startPush('footer'); ?>
       <?php echo $__env->make('core/base::forms.partials.repeater-template', compact('repeaterId', 'defaultFields'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/core/base/resources/views/forms/partials/repeater.blade.php ENDPATH**/ ?>