<div
    id="<?php echo e($id . '-parent'); ?>"
    class="<?php echo \Illuminate\Support\Arr::toCssClasses(['widget-item', 'col-md-' . $columns => $columns]); ?>"
>
    <div class="h-100 position-relative">
        <?php echo $content; ?>

        <?php if($hasChart): ?>
            <div
                class="position-absolute fixed-bottom"
                id="<?php echo e($id); ?>"
                style="z-index: 1"
            ></div>
        <?php endif; ?>
    </div>

    <?php if($hasChart): ?>
        <?php echo $__env->make('core/base::widgets.partials.chart-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
</div>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/core/base/resources/views/widgets/card.blade.php ENDPATH**/ ?>