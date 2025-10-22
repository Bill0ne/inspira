<?php $__env->startSection('content'); ?>
    <?php if($showStart): ?>
        <?php echo Form::open(Arr::except($formOptions, ['template'])); ?>

    <?php endif; ?>

    <?php
        do_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, request(), $form->getModel());
        $columns = $form->getFormOption('columns');
    ?>

    <div class="row">
        <div class="col-md-9">
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
                <?php if (isset($component)) { $__componentOriginalf7ec4b8ef3fc6db54b9665bd653222c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf7ec4b8ef3fc6db54b9665bd653222c4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::card.header.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::card.header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                    <?php if (isset($component)) { $__componentOriginale3e0c938b7d6f2d28b70fc6709c6b9a5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3e0c938b7d6f2d28b70fc6709c6b9a5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::tab.index','data' => ['class' => 'card-header-tabs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::tab'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'card-header-tabs']); ?>
                        <?php if (isset($component)) { $__componentOriginalb01dabf26578858fdcffedb4ad1d59fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb01dabf26578858fdcffedb4ad1d59fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::tab.item','data' => ['id' => 'tabs-detail','isActive' => true,'label' => trans('core/base::tabs.detail')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::tab.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'tabs-detail','is-active' => true,'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('core/base::tabs.detail'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb01dabf26578858fdcffedb4ad1d59fd)): ?>
<?php $attributes = $__attributesOriginalb01dabf26578858fdcffedb4ad1d59fd; ?>
<?php unset($__attributesOriginalb01dabf26578858fdcffedb4ad1d59fd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb01dabf26578858fdcffedb4ad1d59fd)): ?>
<?php $component = $__componentOriginalb01dabf26578858fdcffedb4ad1d59fd; ?>
<?php unset($__componentOriginalb01dabf26578858fdcffedb4ad1d59fd); ?>
<?php endif; ?>

                        <?php echo apply_filters(BASE_FILTER_REGISTER_CONTENT_TABS, null, $form->getModel()); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3e0c938b7d6f2d28b70fc6709c6b9a5)): ?>
<?php $attributes = $__attributesOriginale3e0c938b7d6f2d28b70fc6709c6b9a5; ?>
<?php unset($__attributesOriginale3e0c938b7d6f2d28b70fc6709c6b9a5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3e0c938b7d6f2d28b70fc6709c6b9a5)): ?>
<?php $component = $__componentOriginale3e0c938b7d6f2d28b70fc6709c6b9a5; ?>
<?php unset($__componentOriginale3e0c938b7d6f2d28b70fc6709c6b9a5); ?>
<?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf7ec4b8ef3fc6db54b9665bd653222c4)): ?>
<?php $attributes = $__attributesOriginalf7ec4b8ef3fc6db54b9665bd653222c4; ?>
<?php unset($__attributesOriginalf7ec4b8ef3fc6db54b9665bd653222c4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf7ec4b8ef3fc6db54b9665bd653222c4)): ?>
<?php $component = $__componentOriginalf7ec4b8ef3fc6db54b9665bd653222c4; ?>
<?php unset($__componentOriginalf7ec4b8ef3fc6db54b9665bd653222c4); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal4fdb92edf089f19cd17d37829580c9a6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4fdb92edf089f19cd17d37829580c9a6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::card.body.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::card.body'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                    <?php if (isset($component)) { $__componentOriginalc3d323522182f9489a35c0fa1b349dec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc3d323522182f9489a35c0fa1b349dec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::tab.content','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::tab.content'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                        <?php if (isset($component)) { $__componentOriginal98abe6467e067b33cedd28cf82bdc43d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal98abe6467e067b33cedd28cf82bdc43d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::tab.pane','data' => ['id' => 'tabs-detail','isActive' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::tab.pane'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'tabs-detail','is-active' => true]); ?>
                            <?php if($showFields): ?>
                                <?php echo e($form->getOpenWrapperFormColumns()); ?>


                                <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($field->getName() === $form->getBreakFieldPoint()) break; ?>
                                    <?php unset($fields[$key]); ?>
                                    <?php if(in_array($field->getName(), $exclude)) continue; ?>

                                    <?php echo $field->render(); ?>


                                    <?php if(defined('BASE_FILTER_SLUG_AREA') &&
                                            $field->getName() === SlugHelper::getColumnNameToGenerateSlug($form->getModel())): ?>
                                        <?php echo apply_filters(BASE_FILTER_SLUG_AREA, null, $form->getModel()); ?>

                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <?php echo e($form->getCloseWrapperFormColumns()); ?>

                            <?php endif; ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal98abe6467e067b33cedd28cf82bdc43d)): ?>
<?php $attributes = $__attributesOriginal98abe6467e067b33cedd28cf82bdc43d; ?>
<?php unset($__attributesOriginal98abe6467e067b33cedd28cf82bdc43d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal98abe6467e067b33cedd28cf82bdc43d)): ?>
<?php $component = $__componentOriginal98abe6467e067b33cedd28cf82bdc43d; ?>
<?php unset($__componentOriginal98abe6467e067b33cedd28cf82bdc43d); ?>
<?php endif; ?>
                        <?php echo apply_filters(BASE_FILTER_REGISTER_CONTENT_TAB_INSIDE, null, $form->getModel()); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc3d323522182f9489a35c0fa1b349dec)): ?>
<?php $attributes = $__attributesOriginalc3d323522182f9489a35c0fa1b349dec; ?>
<?php unset($__attributesOriginalc3d323522182f9489a35c0fa1b349dec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc3d323522182f9489a35c0fa1b349dec)): ?>
<?php $component = $__componentOriginalc3d323522182f9489a35c0fa1b349dec; ?>
<?php unset($__componentOriginalc3d323522182f9489a35c0fa1b349dec); ?>
<?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4fdb92edf089f19cd17d37829580c9a6)): ?>
<?php $attributes = $__attributesOriginal4fdb92edf089f19cd17d37829580c9a6; ?>
<?php unset($__attributesOriginal4fdb92edf089f19cd17d37829580c9a6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4fdb92edf089f19cd17d37829580c9a6)): ?>
<?php $component = $__componentOriginal4fdb92edf089f19cd17d37829580c9a6; ?>
<?php unset($__componentOriginal4fdb92edf089f19cd17d37829580c9a6); ?>
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

            <?php $__currentLoopData = $form->getMetaBoxes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $metaBox): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $form->getMetaBox($key); ?>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php
                do_action(BASE_ACTION_META_BOXES, 'advanced', $form->getModel());
            ?>

            <?php echo $__env->yieldContent('form_main_end'); ?>
        </div>
        <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
            <?php echo $form->getActionButtons(); ?>


            <?php
                do_action(BASE_ACTION_META_BOXES, 'top', $form->getModel());
            ?>

            <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(!in_array($field->getName(), $exclude)): ?>
                    <?php if(in_array($field->getType(), ['hidden', \Botble\Base\Forms\Fields\HiddenField::class])): ?>
                        <?php echo $field->render(); ?>

                    <?php else: ?>
                        <?php if (isset($component)) { $__componentOriginalc107e2f90dff5eb05519f33918d2c807 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc107e2f90dff5eb05519f33918d2c807 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::card.index','data' => ['class' => 'meta-boxes']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'meta-boxes']); ?>
                            <?php if (isset($component)) { $__componentOriginalf7ec4b8ef3fc6db54b9665bd653222c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf7ec4b8ef3fc6db54b9665bd653222c4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::card.header.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::card.header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                <?php if (isset($component)) { $__componentOriginal61297c2b6766060b621d6f9a17b28154 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal61297c2b6766060b621d6f9a17b28154 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::card.title','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::card.title'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                    <?php echo Form::customLabel($field->getName(), $field->getOption('label'), $field->getOption('label_attr')); ?>

                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal61297c2b6766060b621d6f9a17b28154)): ?>
<?php $attributes = $__attributesOriginal61297c2b6766060b621d6f9a17b28154; ?>
<?php unset($__attributesOriginal61297c2b6766060b621d6f9a17b28154); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal61297c2b6766060b621d6f9a17b28154)): ?>
<?php $component = $__componentOriginal61297c2b6766060b621d6f9a17b28154; ?>
<?php unset($__componentOriginal61297c2b6766060b621d6f9a17b28154); ?>
<?php endif; ?>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf7ec4b8ef3fc6db54b9665bd653222c4)): ?>
<?php $attributes = $__attributesOriginalf7ec4b8ef3fc6db54b9665bd653222c4; ?>
<?php unset($__attributesOriginalf7ec4b8ef3fc6db54b9665bd653222c4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf7ec4b8ef3fc6db54b9665bd653222c4)): ?>
<?php $component = $__componentOriginalf7ec4b8ef3fc6db54b9665bd653222c4; ?>
<?php unset($__componentOriginalf7ec4b8ef3fc6db54b9665bd653222c4); ?>
<?php endif; ?>

                            <?php if (isset($component)) { $__componentOriginal4fdb92edf089f19cd17d37829580c9a6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4fdb92edf089f19cd17d37829580c9a6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::card.body.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::card.body'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                <?php echo $field->render([], false); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4fdb92edf089f19cd17d37829580c9a6)): ?>
<?php $attributes = $__attributesOriginal4fdb92edf089f19cd17d37829580c9a6; ?>
<?php unset($__attributesOriginal4fdb92edf089f19cd17d37829580c9a6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4fdb92edf089f19cd17d37829580c9a6)): ?>
<?php $component = $__componentOriginal4fdb92edf089f19cd17d37829580c9a6; ?>
<?php unset($__componentOriginal4fdb92edf089f19cd17d37829580c9a6); ?>
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
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php
                do_action(BASE_ACTION_META_BOXES, 'side', $form->getModel());
            ?>
        </div>
    </div>

    <?php if($showEnd): ?>
        <?php echo Form::close(); ?>

    <?php endif; ?>

    <?php echo $__env->yieldContent('form_end'); ?>
<?php $__env->stopSection(); ?>

<?php if($form->getValidatorClass()): ?>
    <?php if($form->isUseInlineJs()): ?>
        <?php echo Assets::scriptToHtml('jquery'); ?>

        <?php echo Assets::scriptToHtml('form-validation'); ?>

        <?php echo $form->renderValidatorJs(); ?>

    <?php else: ?>
        <?php $__env->startPush('footer'); ?>
            <?php echo $form->renderValidatorJs(); ?>

        <?php $__env->stopPush(); ?>
    <?php endif; ?>
<?php endif; ?>

<?php echo $__env->make($layout ?? BaseHelper::getAdminMasterLayoutTemplate(), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/core/base/resources/views/forms/form-tabs.blade.php ENDPATH**/ ?>