<?php echo '<' . '?' . 'xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>

<?php if(null != $style): ?>
    <?php echo '<' . '?' . 'xml-stylesheet href="' . asset($style) . '" type="text/xsl"?>' . "\n"; ?>

<?php endif; ?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php $__currentLoopData = $sitemaps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sitemap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <sitemap>
            <loc><?php echo e($sitemap['loc']); ?></loc>
            <?php if($sitemap['lastmod'] !== null): ?>
                <lastmod><?php echo e(date('Y-m-d\TH:i:sP', strtotime($sitemap['lastmod']))); ?></lastmod>
            <?php endif; ?>
        </sitemap>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</sitemapindex>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/packages/sitemap/resources/views/sitemapindex.blade.php ENDPATH**/ ?>