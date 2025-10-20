<?php if (! $__env->hasRenderedOnce('7dc125e5-d94f-4d5d-a40d-f81755fbdc3f')): $__env->markAsRenderedOnce('7dc125e5-d94f-4d5d-a40d-f81755fbdc3f'); ?>
    <div
        class="offcanvas offcanvas-end"
        tabindex="-1"
        id="notification-sidebar"
        aria-labelledby="notification-sidebar-label"
        data-url="<?php echo e(route('notifications.index')); ?>"
        data-count-url="<?php echo e(route('notifications.count-unread')); ?>"
    >
        <button
            type="button"
            class="btn-close text-reset"
            data-bs-dismiss="offcanvas"
            aria-label="Close"
        ></button>

        <div class="notification-content"></div>
    </div>

    <script src="<?php echo e(asset('vendor/core/core/base/js/notification.js')); ?>"></script>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\inspira\platform/core/base/resources/views/notification/notification.blade.php ENDPATH**/ ?>