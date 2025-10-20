<div
    <?php if(! $width && ! $height): ?>
        style="position: relative; display: block; height: 0; padding-bottom: 56.25%; overflow: hidden;"
    <?php else: ?>
        style="margin-bottom: 20px;"
    <?php endif; ?>
>
    <?php
        $widthStyle = is_int($width) ? "{$width}px" : $width;
    ?>
    <iframe
        src="https://maps.google.com/maps?q=<?php echo e(addslashes($address)); ?>&t=&z=13&ie=UTF8&iwloc=&output=embed"
        style="<?php echo \Illuminate\Support\Arr::toCssStyles(['position: absolute; top: 0; bottom: 0; left: 0; width: 100%; height: 100%; border: 0;' => ! $width && ! $height, "height: {$height}px" => $height, "width: {$widthStyle}" => $width]) ?>"
        title="Google Maps for <?php echo e($address); ?>"
        frameborder="0"
        scrolling="no"
        marginheight="0"
        marginwidth="0"
    ></iframe>
</div>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/packages/theme/resources/views/shortcodes/google-map.blade.php ENDPATH**/ ?>