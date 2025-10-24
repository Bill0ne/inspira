<?php
    $style = in_array($shortcode->style, ['style-1', 'style-2']) ? $shortcode->style : 'style-1';
?>

<?php echo Theme::partial("shortcodes.about-us.styles.$style", compact('shortcode', 'highlightArray')); ?>

<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/partials/shortcodes/about-us/index.blade.php ENDPATH**/ ?>