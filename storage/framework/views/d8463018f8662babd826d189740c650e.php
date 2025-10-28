<?php $__env->startSection('content'); ?>
    <div class="user-profile">
        <?php if (isset($component)) { $__componentOriginalc107e2f90dff5eb05519f33918d2c807 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc107e2f90dff5eb05519f33918d2c807 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::card.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
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
                    <?php if($canChangeProfile): ?>
                        <?php if (isset($component)) { $__componentOriginalb01dabf26578858fdcffedb4ad1d59fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb01dabf26578858fdcffedb4ad1d59fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::tab.item','data' => ['isActive' => true,'id' => 'profile','icon' => 'ti ti-user','label' => ''.e(trans('core/acl::users.info.title')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::tab.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['is-active' => true,'id' => 'profile','icon' => 'ti ti-user','label' => ''.e(trans('core/acl::users.info.title')).'']); ?>
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

                        <?php if (isset($component)) { $__componentOriginalb01dabf26578858fdcffedb4ad1d59fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb01dabf26578858fdcffedb4ad1d59fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::tab.item','data' => ['id' => 'avatar','icon' => 'ti ti-camera-selfie','label' => ''.e(trans('core/acl::users.avatar')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::tab.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'avatar','icon' => 'ti ti-camera-selfie','label' => ''.e(trans('core/acl::users.avatar')).'']); ?>
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

                        <?php if (isset($component)) { $__componentOriginalb01dabf26578858fdcffedb4ad1d59fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb01dabf26578858fdcffedb4ad1d59fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::tab.item','data' => ['id' => 'change-password','icon' => 'ti ti-lock','label' => ''.e(trans('core/acl::users.change_password')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::tab.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'change-password','icon' => 'ti ti-lock','label' => ''.e(trans('core/acl::users.change_password')).'']); ?>
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

                        <?php if (isset($component)) { $__componentOriginalb01dabf26578858fdcffedb4ad1d59fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb01dabf26578858fdcffedb4ad1d59fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::tab.item','data' => ['id' => 'preferences','icon' => 'ti ti-settings','label' => ''.e(trans('core/acl::users.preferences')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::tab.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'preferences','icon' => 'ti ti-settings','label' => ''.e(trans('core/acl::users.preferences')).'']); ?>
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
                    <?php endif; ?>

                    <?php echo apply_filters(ACL_FILTER_PROFILE_FORM_TABS, null); ?>

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
                    <?php if($canChangeProfile): ?>
                        <?php if (isset($component)) { $__componentOriginal98abe6467e067b33cedd28cf82bdc43d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal98abe6467e067b33cedd28cf82bdc43d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::tab.pane','data' => ['id' => 'profile','isActive' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::tab.pane'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'profile','is-active' => true]); ?>
                            <?php echo $form; ?>

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

                        <?php if (isset($component)) { $__componentOriginal98abe6467e067b33cedd28cf82bdc43d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal98abe6467e067b33cedd28cf82bdc43d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::tab.pane','data' => ['id' => 'avatar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::tab.pane'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'avatar']); ?>
                            <?php if (isset($component)) { $__componentOriginald938be4a3580804734cf98c3875c4680 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald938be4a3580804734cf98c3875c4680 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::crop-image','data' => ['label' => trans('core/acl::users.avatar'),'name' => 'avatar_file','value' => $user->avatar_url,'action' => route('users.profile.image', $user->id),'deleteAction' => route('users.profile.image.destroy', $user->getKey()),'canDelete' => $user->avatar_id,'rounded' => 'pill','hiddenCopper' => !$canChangeProfile,'style' => '--bb-avatar-size: 10rem;']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::crop-image'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('core/acl::users.avatar')),'name' => 'avatar_file','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user->avatar_url),'action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('users.profile.image', $user->id)),'delete-action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('users.profile.image.destroy', $user->getKey())),'can-delete' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user->avatar_id),'rounded' => 'pill','hidden-copper' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!$canChangeProfile),'style' => '--bb-avatar-size: 10rem;']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald938be4a3580804734cf98c3875c4680)): ?>
<?php $attributes = $__attributesOriginald938be4a3580804734cf98c3875c4680; ?>
<?php unset($__attributesOriginald938be4a3580804734cf98c3875c4680); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald938be4a3580804734cf98c3875c4680)): ?>
<?php $component = $__componentOriginald938be4a3580804734cf98c3875c4680; ?>
<?php unset($__componentOriginald938be4a3580804734cf98c3875c4680); ?>
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

                        <?php if (isset($component)) { $__componentOriginal98abe6467e067b33cedd28cf82bdc43d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal98abe6467e067b33cedd28cf82bdc43d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::tab.pane','data' => ['id' => 'change-password']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::tab.pane'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'change-password']); ?>
                            <?php echo $passwordForm; ?>

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

                        <?php if (isset($component)) { $__componentOriginal98abe6467e067b33cedd28cf82bdc43d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal98abe6467e067b33cedd28cf82bdc43d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::tab.pane','data' => ['id' => 'preferences']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::tab.pane'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'preferences']); ?>
                            <?php echo $preferenceForm; ?>

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
                    <?php endif; ?>

                    <?php echo apply_filters(ACL_FILTER_PROFILE_FORM_TAB_CONTENTS, null); ?>

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
    </div>
<?php $__env->stopSection(); ?>

<?php if($canChangeProfile): ?>
    <?php $__env->startPush('footer'); ?>
        <?php echo JsValidator::formRequest(Botble\ACL\Http\Requests\UpdateProfileRequest::class, '#profile-form'); ?>

        <?php echo JsValidator::formRequest(Botble\ACL\Http\Requests\UpdatePasswordRequest::class, '#password-form'); ?>

    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php echo $__env->make(BaseHelper::getAdminMasterLayoutTemplate(), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\inspira\platform/core/acl/resources/views/users/profile/base.blade.php ENDPATH**/ ?>