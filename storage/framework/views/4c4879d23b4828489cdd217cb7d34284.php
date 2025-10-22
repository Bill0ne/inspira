<?php if(Auth::user()->hasPermission('room.edit')): ?>
    <a
        class="editable"
        data-type="text"
        data-pk="<?php echo e($item->id); ?>"
        data-url="<?php echo e(route('room.update-order-by')); ?>"
        data-value="<?php echo e($item->order ?? 0); ?>"
        data-title="<?php echo e(trans('core/base::tables.order')); ?>"
        href="#"
    ><?php echo e($item->order ?? 0); ?></a>
<?php else: ?>
    <?php echo e($item->order); ?>

<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/plugins/hotel/resources/views/partials/sort-order.blade.php ENDPATH**/ ?>