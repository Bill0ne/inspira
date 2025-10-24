<?php
    Assets::addScripts('apexchart')->addStyles('apexchart');
?>

<?php $__env->startPush('footer'); ?>
    <script>
        $(document).ready(function() {
            (new ApexCharts(document.getElementById("<?php echo e($id); ?>"), <?php echo e(Js::from($options)); ?>)).render()
        })
    </script>
<?php $__env->stopPush(); ?>

<?php if(request()->ajax()): ?>
    <script>
        (new ApexCharts(document.getElementById("<?php echo e($id); ?>"), <?php echo e(Js::from($options)); ?>)).render()
    </script>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\inspira\platform/core/base/resources/views/widgets/partials/chart-script.blade.php ENDPATH**/ ?>