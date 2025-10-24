<meta
    name="csrf-token"
    content="<?php echo e(csrf_token()); ?>"
>

<?php $__currentLoopData = RvMedia::getConfig('libraries.stylesheets', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $css): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <link
        type="text/css"
        href="<?php echo e(asset($css)); ?>?v=<?php echo e(get_cms_version()); ?>"
        rel="stylesheet"
    />
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php echo $__env->make('core/media::config', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH D:\xampp\htdocs\inspira\platform/core/media/resources/views/header.blade.php ENDPATH**/ ?>