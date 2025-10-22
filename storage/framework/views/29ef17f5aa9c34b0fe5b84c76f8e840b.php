<?php
    $id = Str::camel($attributes->get('id', \Illuminate\Support\Str::random(8)));
?>

<div <?php echo e($attributes->merge(['class' => 'offcanvas offcanvas-start', 'tabindex' => '-1', 'aria-labelledby' => "{$id}Label"])); ?>>
    <?php echo e($slot); ?>

</div>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/core/base/resources/views/components/offcanvas/index.blade.php ENDPATH**/ ?>