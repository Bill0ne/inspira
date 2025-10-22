<?php
    $model = (object)$options['model'];
    $options['prefix'] = SlugHelper::getPrefix($model::class);
?>

<input
    type="hidden"
    name="model"
    value="<?php echo e($model::class); ?>"
>

<?php if(empty($model)): ?>
    <div class="mb-3 <?php if($errors->has($name)): ?> has-error <?php endif; ?>">
        <?php echo Form::permalink($name, old($name), 0, $options['prefix'], [], true, $model); ?>

        <?php echo Form::error($name, $errors); ?>

    </div>
<?php else: ?>
    <div class="mb-3 <?php if($errors->has($name)): ?> has-error <?php endif; ?>">
        <?php echo Form::permalink(
            $name,
            $model->slug,
            $model->slug_id,
            $options['prefix'],
            SlugHelper::canPreview($model) && in_array($model->status, [Botble\Base\Enums\BaseStatusEnum::DRAFT, Botble\Base\Enums\BaseStatusEnum::PENDING]),
            [],
            true,
            $model,
        ); ?>

        <?php echo Form::error($name, $errors); ?>

    </div>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/packages/slug/resources/views/forms/fields/permalink.blade.php ENDPATH**/ ?>