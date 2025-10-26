<?php
    $values = array_values(is_array($options['value']) ? $options['value'] : (array) json_decode($options['value'] ?: '[]', true));
    $fields = $options['fields'];
    $repeaterId = 'repeater_field_' . md5($name) . uniqid('_');
    $added = [];

    if (!empty($values)) {
        foreach ($values as $index => $groupValues) {
            $group = '';
            foreach ($fields as $key => $field) {
                $group .= view('core/base::forms.partials.repeater-item', compact('name', 'index', 'key', 'field', 'values'))->render();
            }
            $added[] = view('core/base::forms.partials.repeater-group', compact('group'))->render();
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
        ])->render();
    }

    $defaultFields = [view('core/base::forms.partials.repeater-group', compact('group'))->render()];
?>

<input name="<?php echo e($name); ?>" type="hidden" value="[]">

<div class="repeater-group" id="<?php echo e($repeaterId); ?>_group" data-next-index="<?php echo e(count($added)); ?>">
    <?php $__currentLoopData = $added; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $field; ?>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="mt-3">
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
        <?php echo e(__('Add new slot')); ?>

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

<?php $__env->startPush('footer'); ?>
    <?php echo $__env->make('core/base::forms.partials.repeater-template', compact('repeaterId', 'defaultFields'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/partials/forms/fields/theme-repeater-field.blade.php ENDPATH**/ ?>