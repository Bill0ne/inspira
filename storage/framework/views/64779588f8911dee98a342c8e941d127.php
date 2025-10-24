<div class="row row-cards">
    <?php $__currentLoopData = $widgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $widget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo e($widget->render()); ?>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php /**PATH D:\xampp\htdocs\inspira\platform/core/base/resources/views/widgets/render.blade.php ENDPATH**/ ?>