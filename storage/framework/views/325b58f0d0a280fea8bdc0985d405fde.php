<?php $__env->startPush('meta-box-header-seo_wrap'); ?>
    <?php if (isset($component)) { $__componentOriginalc35bfc4b98be5180558495d6fb99dd82 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc35bfc4b98be5180558495d6fb99dd82 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::card.actions','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::card.actions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <a href="#" class="btn-trigger-show-seo-detail">
            <?php echo e(trans('packages/seo-helper::seo-helper.edit_seo_meta')); ?>

        </a>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc35bfc4b98be5180558495d6fb99dd82)): ?>
<?php $attributes = $__attributesOriginalc35bfc4b98be5180558495d6fb99dd82; ?>
<?php unset($__attributesOriginalc35bfc4b98be5180558495d6fb99dd82); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc35bfc4b98be5180558495d6fb99dd82)): ?>
<?php $component = $__componentOriginalc35bfc4b98be5180558495d6fb99dd82; ?>
<?php unset($__componentOriginalc35bfc4b98be5180558495d6fb99dd82); ?>
<?php endif; ?>
<?php $__env->stopPush(); ?>

<div
    class="<?php echo \Illuminate\Support\Arr::toCssClasses(['seo-preview', 'noindex' => $meta['index'] === 'noindex']); ?>"
    v-pre
>
    <p class="<?php echo \Illuminate\Support\Arr::toCssClasses(['default-seo-description', 'hidden' => !empty($object->id)]); ?>">
        <?php echo e(trans('packages/seo-helper::seo-helper.default_description')); ?>

    </p>

    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['existed-seo-meta', 'hidden' => empty($object->id)]); ?>">
        <?php if($meta['index'] === 'noindex'): ?>
            <span class="page-index-status">
                <?php if (isset($component)) { $__componentOriginal73995948b3bd877b76251b40caf28170 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal73995948b3bd877b76251b40caf28170 = $attributes; } ?>
<?php $component = Botble\Icon\View\Components\Icon::resolve(['name' => 'ti ti-search-off','size' => 'sm'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Botble\Icon\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-warning']); ?>
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

                <?php echo e(trans('packages/seo-helper::seo-helper.noindex')); ?>

            </span>
        <?php endif; ?>

        <h4 class="page-title-seo text-truncate">
            <?php echo BaseHelper::clean($meta['seo_title'] ?? (!empty($object->id) ? $object->name ?? $object->title : null)); ?>

        </h4>

        <div class="page-url-seo">
            <p><?php echo e(!empty($object->id) && $object->url ? (url(apply_filters(FILTER_SLUG_PREFIX, SlugHelper::getPrefix($object::class), $object)) . '/' . $object->slug) : '-'); ?></p>
        </div>

        <div>
            <span style="color: #70757a;"><?php echo e(!empty($object->id) && $object->created_at ? $object->created_at->format('M d, Y') : Carbon\Carbon::now()->format('M d, Y')); ?>

                - </span>
            <span class="page-description-seo">
                <?php if(!empty($meta['seo_description'])): ?>
                    <?php echo e(Str::limit(strip_tags($meta['seo_description']), 250)); ?>

                <?php elseif($metaDescription = (!empty($object->id) ? ($object->description ?: ($object->content ?: old('seo_meta.seo_description'))) : old('seo_meta.seo_description'))): ?>
                    <?php echo e(Str::limit(strip_tags($metaDescription), 250)); ?>

                <?php endif; ?>
            </span>
        </div>
    </div>
</div>

<div class="hidden seo-edit-section" v-pre>
    <?php if (isset($component)) { $__componentOriginaldf35f1d284a786c742191505b12fd8de = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf35f1d284a786c742191505b12fd8de = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::hr','data' => ['class' => 'my-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::hr'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'my-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf35f1d284a786c742191505b12fd8de)): ?>
<?php $attributes = $__attributesOriginaldf35f1d284a786c742191505b12fd8de; ?>
<?php unset($__attributesOriginaldf35f1d284a786c742191505b12fd8de); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf35f1d284a786c742191505b12fd8de)): ?>
<?php $component = $__componentOriginaldf35f1d284a786c742191505b12fd8de; ?>
<?php unset($__componentOriginaldf35f1d284a786c742191505b12fd8de); ?>
<?php endif; ?>

    <?php echo $form; ?>

</div>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/packages/seo-helper/resources/views/meta-box.blade.php ENDPATH**/ ?>