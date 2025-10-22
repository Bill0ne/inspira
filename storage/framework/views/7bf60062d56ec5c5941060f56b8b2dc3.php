<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'text' => null,
    'textAlignment' => null,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'text' => null,
    'textAlignment' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $classes = Arr::toCssClasses([
        'hr-text' => $text,
        match ($textAlignment) {
            'left' => 'hr-text-left',
            'right' => 'hr-text-right',
            default => null,
        },
    ]);
    
    $tag = $text ? 'div' : 'hr';
?>

<<?php echo e($tag); ?> <?php echo e($attributes->class($classes)); ?>><?php echo e($text); ?></<?php echo e($tag); ?>>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/core/base/resources/views/components/hr.blade.php ENDPATH**/ ?>