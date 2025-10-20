<link
    href="<?php echo e(Language::getLocalizedURL(Language::getDefaultLocale(), url()->current(), [], false)); ?>"
    hreflang="x-default"
    rel="alternate"
/>

<?php if(!empty($urls)): ?>
    <?php $__currentLoopData = $urls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <link
            href="<?php echo e($item['url']); ?>"
            hreflang="<?php echo e($item['lang_code']); ?>"
            rel="alternate"
        />
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php else: ?>
    <?php $__currentLoopData = Language::getSupportedLocales(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeCode => $properties): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <link
            href="<?php echo e(Language::getLocalizedURL($localeCode, url()->current(), [], false)); ?>"
            hreflang="<?php echo e($localeCode); ?>"
            rel="alternate"
        />
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/plugins/language/resources/views/partials/hreflang.blade.php ENDPATH**/ ?>