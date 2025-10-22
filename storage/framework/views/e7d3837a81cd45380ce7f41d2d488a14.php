<a
    class="editable"
    data-type="select"
    data-source="<?php echo e(route('roles.list.json')); ?>"
    data-pk="<?php echo e($item->id); ?>"
    data-url="<?php echo e(route('roles.assign')); ?>"
    data-value="<?php echo e($role?->id ?: 0); ?>"
    data-title="<?php echo e(trans('core/acl::users.assigned_role')); ?>"
    href="#"
>
    <?php echo e($role?->name ?: trans('core/acl::users.no_role_assigned')); ?>

</a>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/core/acl/resources/views/users/partials/role.blade.php ENDPATH**/ ?>