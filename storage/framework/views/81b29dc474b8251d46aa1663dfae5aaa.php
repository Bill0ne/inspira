<calendar-modal-component
    get-room-availability-url="<?php echo e(route('room.availability', $object->id)); ?>"
    v-slot="{ form, saveForm }"
>
    <div id="dates-calendar" class="dates-calendar"></div>

    <?php if (isset($component)) { $__componentOriginaldc8ac54b6bf7eb0d0560fdd5aa630687 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldc8ac54b6bf7eb0d0560fdd5aa630687 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::modal','data' => ['id' => 'modal-calendar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modal-calendar']); ?>
        <div class="mb-3">
            <?php if (isset($component)) { $__componentOriginal424617256517489644ca6a2e02d16322 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal424617256517489644ca6a2e02d16322 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::form.checkbox','data' => ['label' => __('plugins/hotel::room.form.is_available'),'trueValue' => '1','falseValue' => '0','vModel' => 'form.active']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::form.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('plugins/hotel::room.form.is_available')),'true-value' => '1','false-value' => '0','v-model' => 'form.active']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal424617256517489644ca6a2e02d16322)): ?>
<?php $attributes = $__attributesOriginal424617256517489644ca6a2e02d16322; ?>
<?php unset($__attributesOriginal424617256517489644ca6a2e02d16322); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal424617256517489644ca6a2e02d16322)): ?>
<?php $component = $__componentOriginal424617256517489644ca6a2e02d16322; ?>
<?php unset($__componentOriginal424617256517489644ca6a2e02d16322); ?>
<?php endif; ?>
        </div>

        <div v-show="form.active">
            <?php if (isset($component)) { $__componentOriginala5b2ce8ea835a1a6ed10854da20fa051 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala5b2ce8ea835a1a6ed10854da20fa051 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::form.text-input','data' => ['type' => 'number','name' => 'room_availability_value','label' => __('plugins/hotel::room.form.value'),'vModel' => 'form.value']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::form.text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'number','name' => 'room_availability_value','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('plugins/hotel::room.form.value')),'v-model' => 'form.value']); ?>
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

            <?php if (isset($component)) { $__componentOriginald8f3cab0e02bd6920e9589a31228d9ca = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8f3cab0e02bd6920e9589a31228d9ca = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::form.select','data' => ['vShow' => 'form.active','name' => 'room_availability_value_type','vModel' => 'form.value_type']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::form.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['v-show' => 'form.active','name' => 'room_availability_value_type','v-model' => 'form.value_type']); ?>
                <option :selected="form.value_type === 'fixed'" value="fixed">
                    <?php echo e(__('plugins/hotel::room.form.fixed')); ?>

                </option>
                <option
                    :selected="form.value_type === 'amount_adjust'"
                    value="amount_adjust"
                >
                    <?php echo e(__('plugins/hotel::room.form.amount_adjust')); ?>

                </option>
                <option
                    :selected="form.value_type === 'percentage_adjust'"
                    value="percentage_adjust"
                >
                    <?php echo e(__('plugins/hotel::room.form.percentage_adjust')); ?>

                </option>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald8f3cab0e02bd6920e9589a31228d9ca)): ?>
<?php $attributes = $__attributesOriginald8f3cab0e02bd6920e9589a31228d9ca; ?>
<?php unset($__attributesOriginald8f3cab0e02bd6920e9589a31228d9ca); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald8f3cab0e02bd6920e9589a31228d9ca)): ?>
<?php $component = $__componentOriginald8f3cab0e02bd6920e9589a31228d9ca; ?>
<?php unset($__componentOriginald8f3cab0e02bd6920e9589a31228d9ca); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginalecda78b9fe8916cbd83b85e55a8b7a1c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalecda78b9fe8916cbd83b85e55a8b7a1c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::alert','data' => ['type' => 'info','vIf' => 'form.value_type === \'percentage_adjust\'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'info','v-if' => 'form.value_type === \'percentage_adjust\'']); ?>
                <?php echo e(__('Adjust the value within a range of -100% to unlimited !')); ?>

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

            <?php if (isset($component)) { $__componentOriginala5b2ce8ea835a1a6ed10854da20fa051 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala5b2ce8ea835a1a6ed10854da20fa051 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::form.text-input','data' => ['label' => __('plugins/hotel::room.form.number_of_rooms'),'type' => 'number','name' => 'room_availability_number_of_rooms','vModel' => 'form.number_of_rooms']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::form.text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('plugins/hotel::room.form.number_of_rooms')),'type' => 'number','name' => 'room_availability_number_of_rooms','v-model' => 'form.number_of_rooms']); ?>
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
        </div>

         <?php $__env->slot('footer', null, []); ?> 
            <?php if (isset($component)) { $__componentOriginal922f7d3260a518f4cf606eecf9669dcb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal922f7d3260a518f4cf606eecf9669dcb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::button','data' => ['dataBsDismiss' => 'modal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['data-bs-dismiss' => 'modal']); ?>
                <?php echo e(__('plugins/hotel::room.form.close')); ?>

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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::button','data' => ['color' => 'primary','@click' => 'saveForm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'primary','@click' => 'saveForm']); ?>
                <?php echo e(__('plugins/hotel::room.form.save_changes')); ?>

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
         <?php $__env->endSlot(); ?>
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
</calendar-modal-component>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/plugins/hotel/resources/views/forms/room-availability.blade.php ENDPATH**/ ?>