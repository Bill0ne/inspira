<?php $__env->startPush('header-action'); ?>
    <?php if(
        $isEnabledMarketplaceFeature = config('packages.plugin-management.general.enable_marketplace_feature')
        && auth()->user()->hasPermission('plugins.marketplace')
    ): ?>
        <?php if (isset($component)) { $__componentOriginal922f7d3260a518f4cf606eecf9669dcb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal922f7d3260a518f4cf606eecf9669dcb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::button','data' => ['tag' => 'a','href' => route('plugins.new'),'color' => 'primary','icon' => 'ti ti-plus','class' => 'ms-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tag' => 'a','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('plugins.new')),'color' => 'primary','icon' => 'ti ti-plus','class' => 'ms-auto']); ?>
            <?php echo e(trans('packages/plugin-management::plugin.plugins_add_new')); ?>

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
    <?php endif; ?>

    <?php echo apply_filters('plugin_management_installed_header_actions', null); ?>

<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <?php if($plugins->isNotEmpty()): ?>
        <div class="d-flex gap-2 justify-content-between">
            <div class="w-100 w-sm-25">
                <?php if (isset($component)) { $__componentOriginala5b2ce8ea835a1a6ed10854da20fa051 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala5b2ce8ea835a1a6ed10854da20fa051 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::form.text-input','data' => ['type' => 'search','name' => 'search','placeholder' => trans('packages/plugin-management::plugin.search'),'groupFlat' => true,'dataBbToggle' => 'change-search']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::form.text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'search','name' => 'search','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('packages/plugin-management::plugin.search')),'group-flat' => true,'data-bb-toggle' => 'change-search']); ?>
                     <?php $__env->slot('prepend', null, []); ?> 
                        <span class="input-group-text">
                            <?php if (isset($component)) { $__componentOriginal73995948b3bd877b76251b40caf28170 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal73995948b3bd877b76251b40caf28170 = $attributes; } ?>
<?php $component = Botble\Icon\View\Components\Icon::resolve(['name' => 'ti ti-search'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                        </span>
                     <?php $__env->endSlot(); ?>
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

            <div class="col-auto">
                <div class="d-block d-sm-none dropdown">
                    <?php if (isset($component)) { $__componentOriginal922f7d3260a518f4cf606eecf9669dcb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal922f7d3260a518f4cf606eecf9669dcb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::button','data' => ['class' => 'dropdown-toggle','dataBsToggle' => 'dropdown']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'dropdown-toggle','data-bs-toggle' => 'dropdown']); ?>
                        <span data-bb-toggle="status-filter-label" class="ms-1">
                            <?php echo e($filterStatuses[array_key_first($filterStatuses)]); ?>

                            (<span data-bb-toggle="plugins-count" data-status="<?php echo e(array_key_first($filterStatuses)); ?>"><?php echo e($plugins->count()); ?></span>)
                        </span>
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
                    <div class="dropdown-menu dropdown-menu-end" data-popper-placement="bottom-end">
                        <?php $__currentLoopData = $filterStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button class="<?php echo \Illuminate\Support\Arr::toCssClasses(['dropdown-item', 'active' => $loop->first]); ?>" type="button" data-value="<?php echo e($key); ?>" data-bb-toggle="change-filter-plugin-status">
                                <?php echo e($value); ?>

                                (<span data-bb-toggle="plugins-count" data-status="<?php echo e($key); ?>">0</span>)
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <div class="d-none d-sm-flex form-selectgroup">
                    <?php $__currentLoopData = $filterStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="form-selectgroup-item">
                            <input type="radio" name="status" value="<?php echo e($key); ?>" data-bb-toggle="change-filter-plugin-status" class="form-selectgroup-input" <?php if($loop->first): echo 'checked'; endif; ?> />
                            <span class="form-selectgroup-label">
                                <?php echo e($value); ?>

                                (<span data-bb-toggle="plugins-count" data-status="<?php echo e($key); ?>">0</span>)
                            </span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-4 row-cards plugin-list">
            <?php $__currentLoopData = $plugins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plugin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div
                    class="col plugin-item"
                    data-name="<?php echo e($plugin->name); ?>"
                    data-author="<?php echo e($plugin->author); ?>"
                    data-description="<?php echo e($plugin->description); ?>"
                    data-status="<?php echo e($plugin->status ? 'activated' : 'not-activated'); ?>"
                >
                    <?php if (isset($component)) { $__componentOriginalc107e2f90dff5eb05519f33918d2c807 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc107e2f90dff5eb05519f33918d2c807 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::card.index','data' => ['class' => 'h-100']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-100']); ?>
                        <div
                            class="position-relative img-responsive img-responsive-3x1 card-img-top border-bottom"
                            style="<?php echo \Illuminate\Support\Arr::toCssStyles(['background-color: #efefef', "background-image: url('$plugin->image')" => $plugin->image]) ?>"
                        >
                            <?php if(! $plugin->image): ?>
                                <?php if (isset($component)) { $__componentOriginal73995948b3bd877b76251b40caf28170 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal73995948b3bd877b76251b40caf28170 = $attributes; } ?>
<?php $component = Botble\Icon\View\Components\Icon::resolve(['name' => 'ti ti-puzzle','size' => 'lg'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Botble\Icon\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'position-absolute','style' => 'top: calc(50% - 28px); left: calc(50% - 28px)']); ?>
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
                            <?php endif; ?>
                        </div>

                        <?php if (isset($component)) { $__componentOriginal4fdb92edf089f19cd17d37829580c9a6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4fdb92edf089f19cd17d37829580c9a6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::card.body.index','data' => ['class' => 'd-flex flex-column justify-content-between']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::card.body'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'd-flex flex-column justify-content-between']); ?>
                            <div>
                                <?php if (isset($component)) { $__componentOriginal61297c2b6766060b621d6f9a17b28154 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal61297c2b6766060b621d6f9a17b28154 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::card.title','data' => ['class' => 'text-truncate mb-2','title' => ''.e($plugin->name).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::card.title'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-truncate mb-2','title' => ''.e($plugin->name).'']); ?>
                                    <?php echo e($plugin->name); ?>

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
                                <?php if($plugin->description): ?>
                                    <p class="text-secondary text-truncate" title="<?php echo e($plugin->description); ?>">
                                        <?php echo e($plugin->description); ?>

                                    </p>
                                <?php endif; ?>
                            </div>

                            <div class="row g-1 g-lg-0">
                                <?php if(!config('packages.plugin-management.general.hide_plugin_author', false) && $plugin->author): ?>
                                    <div class="col-12 col-lg">
                                        <?php echo e(trans('packages/plugin-management::plugin.author')); ?>:
                                        <?php if(! empty($plugin->url)): ?>
                                            <a href="<?php echo e($plugin->url); ?>" target="_blank" class="fw-bold"><?php echo e($plugin->author); ?></a>
                                        <?php else: ?>
                                            <strong><?php echo e($plugin->author); ?></strong>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if($plugin->version): ?>
                                    <div class="col-12 col-lg-auto">
                                        <?php echo e(trans('packages/plugin-management::plugin.version')); ?>:
                                        <strong><?php echo e($plugin->version); ?></strong>
                                    </div>
                                <?php endif; ?>
                            </div>
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

                        <?php if (isset($component)) { $__componentOriginal00609f0158ec6107e317b89bf18d2d23 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal00609f0158ec6107e317b89bf18d2d23 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::card.footer.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::card.footer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                            <div class="btn-list">
                                <?php if(auth()->user()->hasPermission('plugins.edit')): ?>
                                    <?php if (isset($component)) { $__componentOriginal922f7d3260a518f4cf606eecf9669dcb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal922f7d3260a518f4cf606eecf9669dcb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::button','data' => ['type' => 'button','color' => $plugin->status ? 'warning' : 'primary','class' => 'btn-trigger-change-status','dataPlugin' => ''.e($plugin->path).'','dataStatus' => ''.e($plugin->status).'','dataCheckRequirementUrl' => route('plugins.check-requirement', ['name' => $plugin->path]),'dataChangeStatusUrl' => route('plugins.change.status', ['name' => $plugin->path])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($plugin->status ? 'warning' : 'primary'),'class' => 'btn-trigger-change-status','data-plugin' => ''.e($plugin->path).'','data-status' => ''.e($plugin->status).'','data-check-requirement-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('plugins.check-requirement', ['name' => $plugin->path])),'data-change-status-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('plugins.change.status', ['name' => $plugin->path]))]); ?>
                                        <?php if($plugin->status): ?>
                                            <?php echo e(trans('packages/plugin-management::plugin.deactivate')); ?>

                                        <?php else: ?>
                                            <?php echo e(trans('packages/plugin-management::plugin.activate')); ?>

                                        <?php endif; ?>
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
                                <?php endif; ?>

                                <?php if($isEnabledMarketplaceFeature): ?>
                                    <?php if (isset($component)) { $__componentOriginal922f7d3260a518f4cf606eecf9669dcb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal922f7d3260a518f4cf606eecf9669dcb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::button','data' => ['class' => 'btn-trigger-update-plugin','color' => 'success','style' => 'display: none;','dataName' => ''.e($plugin->path).'','dataCheckUpdate' => ''.e($plugin->id ?? 'plugin-' . $plugin->path).'','dataCheckUpdateUrl' => route('plugins.marketplace.ajax.check-update'),'dataUpdateUrl' => route('plugins.marketplace.ajax.update', ['id' => '__id__', 'name' => $plugin->path]),'dataVersion' => ''.e($plugin->version).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'btn-trigger-update-plugin','color' => 'success','style' => 'display: none;','data-name' => ''.e($plugin->path).'','data-check-update' => ''.e($plugin->id ?? 'plugin-' . $plugin->path).'','data-check-update-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('plugins.marketplace.ajax.check-update')),'data-update-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('plugins.marketplace.ajax.update', ['id' => '__id__', 'name' => $plugin->path])),'data-version' => ''.e($plugin->version).'']); ?>
                                        <?php echo e(trans('packages/plugin-management::plugin.update')); ?>

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
                                <?php endif; ?>

                                <?php if(auth()->user()->hasPermission('plugins.remove')): ?>
                                    <?php if (isset($component)) { $__componentOriginal922f7d3260a518f4cf606eecf9669dcb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal922f7d3260a518f4cf606eecf9669dcb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::button','data' => ['type' => 'button','class' => 'btn-trigger-remove-plugin','dataPlugin' => ''.e($plugin->path).'','dataUrl' => route('plugins.remove', ['plugin' => $plugin->path])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','class' => 'btn-trigger-remove-plugin','data-plugin' => ''.e($plugin->path).'','data-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('plugins.remove', ['plugin' => $plugin->path]))]); ?>
                                        <?php echo e(trans('packages/plugin-management::plugin.remove')); ?>

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
                                <?php endif; ?>
                            </div>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal00609f0158ec6107e317b89bf18d2d23)): ?>
<?php $attributes = $__attributesOriginal00609f0158ec6107e317b89bf18d2d23; ?>
<?php unset($__attributesOriginal00609f0158ec6107e317b89bf18d2d23); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal00609f0158ec6107e317b89bf18d2d23)): ?>
<?php $component = $__componentOriginal00609f0158ec6107e317b89bf18d2d23; ?>
<?php unset($__componentOriginal00609f0158ec6107e317b89bf18d2d23); ?>
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
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($component)) { $__componentOriginald9d6100f07b8c41618767a130852b3e8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald9d6100f07b8c41618767a130852b3e8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::empty-state','data' => ['title' => trans('No plugins found'),'subtitle' => trans('It looks as there are no plugins here.'),'style' => \Illuminate\Support\Arr::toCssStyles(['display: none' => $plugins->isNotEmpty()])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('No plugins found')),'subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('It looks as there are no plugins here.')),'style' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssStyles(['display: none' => $plugins->isNotEmpty()]))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald9d6100f07b8c41618767a130852b3e8)): ?>
<?php $attributes = $__attributesOriginald9d6100f07b8c41618767a130852b3e8; ?>
<?php unset($__attributesOriginald9d6100f07b8c41618767a130852b3e8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald9d6100f07b8c41618767a130852b3e8)): ?>
<?php $component = $__componentOriginald9d6100f07b8c41618767a130852b3e8; ?>
<?php unset($__componentOriginald9d6100f07b8c41618767a130852b3e8); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer'); ?>
    <?php if (isset($component)) { $__componentOriginal9376784f974ff66f3ff18195ab0a89c5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9376784f974ff66f3ff18195ab0a89c5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::modal.action','data' => ['id' => 'remove-plugin-modal','type' => 'danger','title' => trans('packages/plugin-management::plugin.remove_plugin'),'description' => trans('packages/plugin-management::plugin.remove_plugin_confirm_message'),'submitButtonAttrs' => ['id' => 'confirm-remove-plugin-button'],'submitButtonLabel' => trans('packages/plugin-management::plugin.remove_plugin_confirm_yes')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::modal.action'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'remove-plugin-modal','type' => 'danger','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('packages/plugin-management::plugin.remove_plugin')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('packages/plugin-management::plugin.remove_plugin_confirm_message')),'submit-button-attrs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['id' => 'confirm-remove-plugin-button']),'submit-button-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('packages/plugin-management::plugin.remove_plugin_confirm_yes'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9376784f974ff66f3ff18195ab0a89c5)): ?>
<?php $attributes = $__attributesOriginal9376784f974ff66f3ff18195ab0a89c5; ?>
<?php unset($__attributesOriginal9376784f974ff66f3ff18195ab0a89c5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9376784f974ff66f3ff18195ab0a89c5)): ?>
<?php $component = $__componentOriginal9376784f974ff66f3ff18195ab0a89c5; ?>
<?php unset($__componentOriginal9376784f974ff66f3ff18195ab0a89c5); ?>
<?php endif; ?>

    <?php if($isEnabledMarketplaceFeature): ?>
        <?php if (isset($component)) { $__componentOriginaldc8ac54b6bf7eb0d0560fdd5aa630687 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldc8ac54b6bf7eb0d0560fdd5aa630687 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::modal','data' => ['id' => 'confirm-install-plugin-modal','title' => trans('packages/plugin-management::plugin.install_plugin'),'buttonId' => 'confirm-install-plugin-button','buttonLabel' => trans('packages/plugin-management::plugin.install')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'confirm-install-plugin-modal','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('packages/plugin-management::plugin.install_plugin')),'button-id' => 'confirm-install-plugin-button','button-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('packages/plugin-management::plugin.install'))]); ?>
            <input
                type="hidden"
                name="plugin_name"
                value=""
            >
            <input
                type="hidden"
                name="ids"
                value=""
            >

            <p id="requirement-message"></p>
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
    <?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make(BaseHelper::getAdminMasterLayoutTemplate(), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/packages/plugin-management/resources/views/index.blade.php ENDPATH**/ ?>