<?php if(($columns = $field->getParent()->getFormOption('columns')) && $hasColspan =  $field->getOption('colspan')): ?>
    <div
        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            "col-{$field->getColumnSpan('default')}" => $field->getColumnSpan('default'),
            "col-sm-{$field->getColumnSpan('sm')}" => $field->getColumnSpan('sm'),
            "col-md-{$field->getColumnSpan('md')}" => $field->getColumnSpan('md'),
            "col-lg-{$field->getColumnSpan('lg')}" => $field->getColumnSpan('lg'),
            "col-xl-{$field->getColumnSpan('xl')}" => $field->getColumnSpan('xl'),
            "col-xxl-{$field->getColumnSpan('xxl')}" => $field->getColumnSpan('xxl'),
        ]); ?>"
    >
        <?php endif; ?>
        <?php echo $html; ?>

        <?php if($columns && $hasColspan): ?>
    </div>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/core/base/resources/views/forms/columns/column-span.blade.php ENDPATH**/ ?>