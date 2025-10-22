<?php if(!Arr::get($attributes, 'without-buttons', false)): ?>
    <?php
        $id = Arr::get($attributes, 'id', $name);
        Arr::set($attributes, 'id', $id);
    ?>

    <div class="mb-2 btn-list">
        <?php if (isset($component)) { $__componentOriginal922f7d3260a518f4cf606eecf9669dcb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal922f7d3260a518f4cf606eecf9669dcb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::button','data' => ['type' => 'button','dataResult' => ''.e($id).'','class' => 'show-hide-editor-btn']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','data-result' => ''.e($id).'','class' => 'show-hide-editor-btn']); ?>
            <?php echo e(trans('core/base::forms.show_hide_editor')); ?>

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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::button','data' => ['type' => 'button','icon' => 'ti ti-photo','class' => 'btn_gallery','dataResult' => ''.e($id).'','dataMultiple' => 'true','dataAction' => 'media-insert-'.e(BaseHelper::getRichEditor()).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','icon' => 'ti ti-photo','class' => 'btn_gallery','data-result' => ''.e($id).'','data-multiple' => 'true','data-action' => 'media-insert-'.e(BaseHelper::getRichEditor()).'']); ?>
            <?php echo e(trans('core/media::media.add')); ?>

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

        <?php echo apply_filters(BASE_FILTER_FORM_EDITOR_BUTTONS, null, $attributes, $id); ?>

    </div>

    <?php $__env->startPush('header'); ?>
        <?php echo apply_filters(BASE_FILTER_FORM_EDITOR_BUTTONS_HEADER, null, $attributes, $id); ?>

    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('footer'); ?>
        <?php echo apply_filters(BASE_FILTER_FORM_EDITOR_BUTTONS_FOOTER, null, $attributes, $id); ?>

    <?php $__env->stopPush(); ?>
<?php else: ?>
    <?php Arr::forget($attributes, 'with-short-code'); ?>
<?php endif; ?>

<?php echo call_user_func_array([Form::class, BaseHelper::getRichEditor()], [$name, $value, $attributes]); ?>

<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/core/base/resources/views/forms/partials/editor.blade.php ENDPATH**/ ?>