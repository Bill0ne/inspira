<?php
    /** @var \Botble\Table\Abstracts\TableAbstract $table */
    /** @var \Botble\Table\Abstracts\TableActionAbstract[] $actions */
    /** @var \Illuminate\Database\Eloquent\Model $model */
?>

<div class="table-actions">
    <?php if(! $table->hasDisplayActionsAsDropdown()): ?>
        <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo e($action->setItem($model)); ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        <div class="dropdown">
            <button class="btn dropdown-toggle" type="button" id="<?php echo e($id = sprintf('dropdown-actions-%s-%s', md5($model::class), $model->getKey())); ?>" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?php echo e(trans('core/base::tables.action')); ?>

            </button>
            <div class="dropdown-menu" aria-labelledby="<?php echo e($id); ?>">
                <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo e($action->setItem($model)->displayAsDropdownItem()); ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/core/table/resources/views/row-actions.blade.php ENDPATH**/ ?>