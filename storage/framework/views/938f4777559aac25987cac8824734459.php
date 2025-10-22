<?php if (isset($component)) { $__componentOriginalc0cc3528153402540de7319c5c012ee8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc0cc3528153402540de7319c5c012ee8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::datagrid.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::datagrid'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php if (isset($component)) { $__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::datagrid.item','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::datagrid.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
         <?php $__env->slot('title', null, []); ?> <?php echo e(trans('plugins/contact::contact.tables.full_name')); ?> <?php $__env->endSlot(); ?>
        <?php echo e($contact->name); ?>

     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac)): ?>
<?php $attributes = $__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac; ?>
<?php unset($__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac)): ?>
<?php $component = $__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac; ?>
<?php unset($__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::datagrid.item','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::datagrid.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
         <?php $__env->slot('title', null, []); ?> <?php echo e(trans('plugins/contact::contact.tables.email')); ?> <?php $__env->endSlot(); ?>
        <?php echo e(Html::mailto($contact->email)); ?>

     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac)): ?>
<?php $attributes = $__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac; ?>
<?php unset($__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac)): ?>
<?php $component = $__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac; ?>
<?php unset($__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac); ?>
<?php endif; ?>

    <?php if($contact->phone): ?>
        <?php if (isset($component)) { $__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::datagrid.item','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::datagrid.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
             <?php $__env->slot('title', null, []); ?> <?php echo e(trans('plugins/contact::contact.tables.phone')); ?> <?php $__env->endSlot(); ?>
            <a href="tel:<?php echo e($contact->phone); ?>"><?php echo e($contact->phone); ?></a>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac)): ?>
<?php $attributes = $__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac; ?>
<?php unset($__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac)): ?>
<?php $component = $__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac; ?>
<?php unset($__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac); ?>
<?php endif; ?>
    <?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::datagrid.item','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::datagrid.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
         <?php $__env->slot('title', null, []); ?> <?php echo e(trans('plugins/contact::contact.tables.time')); ?> <?php $__env->endSlot(); ?>
        <?php echo e($contact->created_at->translatedFormat('d M Y H:i:s')); ?>

     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac)): ?>
<?php $attributes = $__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac; ?>
<?php unset($__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac)): ?>
<?php $component = $__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac; ?>
<?php unset($__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac); ?>
<?php endif; ?>

    <?php if($contact->address): ?>
        <?php if (isset($component)) { $__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::datagrid.item','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::datagrid.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
             <?php $__env->slot('title', null, []); ?> <?php echo e(trans('plugins/contact::contact.tables.address')); ?> <?php $__env->endSlot(); ?>
            <?php echo e($contact->address); ?>

         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac)): ?>
<?php $attributes = $__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac; ?>
<?php unset($__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac)): ?>
<?php $component = $__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac; ?>
<?php unset($__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac); ?>
<?php endif; ?>
    <?php endif; ?>

    <?php if($contact->custom_fields): ?>
        <?php $__currentLoopData = $contact->custom_fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(blank($value)) continue; ?>
            <?php if (isset($component)) { $__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::datagrid.item','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::datagrid.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                 <?php $__env->slot('title', null, []); ?> <?php echo e($key); ?> <?php $__env->endSlot(); ?>
                <?php echo e($value); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac)): ?>
<?php $attributes = $__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac; ?>
<?php unset($__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac)): ?>
<?php $component = $__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac; ?>
<?php unset($__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac); ?>
<?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    <?php if($contact->subject): ?>
        <?php if (isset($component)) { $__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::datagrid.item','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::datagrid.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
             <?php $__env->slot('title', null, []); ?> <?php echo e(trans('plugins/contact::contact.tables.subject')); ?> <?php $__env->endSlot(); ?>
            <?php echo e($contact->subject); ?>

         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac)): ?>
<?php $attributes = $__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac; ?>
<?php unset($__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac)): ?>
<?php $component = $__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac; ?>
<?php unset($__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac); ?>
<?php endif; ?>
    <?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc0cc3528153402540de7319c5c012ee8)): ?>
<?php $attributes = $__attributesOriginalc0cc3528153402540de7319c5c012ee8; ?>
<?php unset($__attributesOriginalc0cc3528153402540de7319c5c012ee8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc0cc3528153402540de7319c5c012ee8)): ?>
<?php $component = $__componentOriginalc0cc3528153402540de7319c5c012ee8; ?>
<?php unset($__componentOriginalc0cc3528153402540de7319c5c012ee8); ?>
<?php endif; ?>

<?php if (isset($component)) { $__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::datagrid.item','data' => ['class' => 'mt-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::datagrid.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mt-3']); ?>
     <?php $__env->slot('title', null, []); ?> <?php echo e(trans('plugins/contact::contact.tables.content')); ?> <?php $__env->endSlot(); ?>
    <?php echo e($contact->content ?: '...'); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac)): ?>
<?php $attributes = $__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac; ?>
<?php unset($__attributesOriginal9d1723e55a4fe6f8bd7b5292f882d2ac); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac)): ?>
<?php $component = $__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac; ?>
<?php unset($__componentOriginal9d1723e55a4fe6f8bd7b5292f882d2ac); ?>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/plugins/contact/resources/views/contact-info.blade.php ENDPATH**/ ?>