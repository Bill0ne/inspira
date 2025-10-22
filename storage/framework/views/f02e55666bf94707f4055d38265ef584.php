<?php echo '<' . '?' . 'xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>

<?php if(null != $style): ?>
    <?php echo '<' . '?' . 'xml-stylesheet href="' . asset($style) . '" type="text/xsl"?>' . "\n"; ?>

<?php endif; ?>
<urlset
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xhtml="http://www.w3.org/1999/xhtml"
    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
    xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"
>
    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <url>
            <loc><?php echo e($item['loc']); ?></loc>
            <?php if(!empty($item['translations'])): ?>
                <?php $__currentLoopData = $item['translations']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $translation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <xhtml:link
                        hreflang="<?php echo e($translation['language']); ?>"
                        href="<?php echo e($translation['url']); ?>"
                        rel="alternate"
                    />
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>

            <?php if(!empty($item['alternates'])): ?>
                <?php $__currentLoopData = $item['alternates']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alternate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <xhtml:link
                        href="<?php echo e($alternate['url']); ?>"
                        rel="alternate"
                        media="<?php echo e($alternate['media']); ?>"
                    />
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>

            <?php if($item['priority'] !== null): ?>
                <priority><?php echo e($item['priority']); ?></priority>
            <?php endif; ?>

            <?php if($item['lastmod'] !== null): ?>
                <lastmod><?php echo e(date('Y-m-d\TH:i:sP', strtotime($item['lastmod']))); ?></lastmod>
            <?php endif; ?>

            <?php if($item['freq'] !== null): ?>
                <changefreq><?php echo e($item['freq']); ?></changefreq>
            <?php endif; ?>

            <?php if(!empty($item['images'])): ?>
                <?php $__currentLoopData = $item['images']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <image:image>
                        <image:loc><?php echo e($image['url']); ?></image:loc>
                        <?php if(isset($image['title'])): ?>
                            <image:title><?php echo e($image['title']); ?></image:title>
                        <?php endif; ?>
                        <?php if(isset($image['caption'])): ?>
                            <image:caption><?php echo e($image['caption']); ?></image:caption>
                        <?php endif; ?>
                        <?php if(isset($image['geo_location'])): ?>
                            <image:geo_location><?php echo e($image['geo_location']); ?></image:geo_location>
                        <?php endif; ?>
                        <?php if(isset($image['license'])): ?>
                            <image:license><?php echo e($image['license']); ?></image:license>
                        <?php endif; ?>
                    </image:image>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>

            <?php if(!empty($item['videos'])): ?>
                <?php $__currentLoopData = $item['videos']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $video): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <video:video>
                        <?php if(isset($video['thumbnail_loc'])): ?>
                            <video:thumbnail_loc><?php echo e($video['thumbnail_loc']); ?></video:thumbnail_loc>
                        <?php endif; ?>
                        <?php if(isset($video['title'])): ?>
                            <video:title>
                                <![CDATA[<?php echo e($video['title']); ?>]]>
                            </video:title>
                        <?php endif; ?>
                        <?php if(isset($video['description'])): ?>
                            <video:description>
                                <![CDATA[<?php echo e($video['description']); ?>]]>
                            </video:description>
                        <?php endif; ?>
                        <?php if(isset($video['content_loc'])): ?>
                            <video:content_loc><?php echo e($video['content_loc']); ?></video:content_loc>
                        <?php endif; ?>
                        <?php if(isset($video['duration'])): ?>
                            <video:duration><?php echo e($video['duration']); ?></video:duration>
                        <?php endif; ?>
                        <?php if(isset($video['expiration_date'])): ?>
                            <video:expiration_date><?php echo e($video['expiration_date']); ?></video:expiration_date>
                        <?php endif; ?>
                        <?php if(isset($video['rating'])): ?>
                            <video:rating><?php echo e($video['rating']); ?></video:rating>
                        <?php endif; ?>
                        <?php if(isset($video['view_count'])): ?>
                            <video:view_count><?php echo e($video['view_count']); ?></video:view_count>
                        <?php endif; ?>
                        <?php if(isset($video['publication_date'])): ?>
                            <video:publication_date><?php echo e($video['publication_date']); ?></video:publication_date>
                        <?php endif; ?>
                        <?php if(isset($video['family_friendly'])): ?>
                            <video:family_friendly><?php echo e($video['family_friendly']); ?></video:family_friendly>
                        <?php endif; ?>
                        <?php if(isset($video['requires_subscription'])): ?>
                            <video:requires_subscription><?php echo e($video['requires_subscription']); ?>

                            </video:requires_subscription>
                        <?php endif; ?>
                        <?php if(isset($video['live'])): ?>
                            <video:live><?php echo e($video['live']); ?></video:live>
                        <?php endif; ?>
                        <?php if(isset($video['player_loc'])): ?>
                            <video:player_loc
                                allow_embed="<?php echo e($video['player_loc']['allow_embed']); ?>"
                                autoplay="' .
                            $video['player_loc']['autoplay'] }}"
                            ><?php echo e($video['player_loc']['player_loc']); ?></video:player_loc>
                        <?php endif; ?>
                        <?php if(isset($video['restriction'])): ?>
                            <video:restriction relationship="<?php echo e($video['restriction']['relationship']); ?>">
                                <?php echo e($video['restriction']['restriction']); ?></video:restriction>
                        <?php endif; ?>
                        <?php if(isset($video['gallery_loc'])): ?>
                            <video:gallery_loc title="<?php echo e($video['gallery_loc']['title']); ?>">
                                <?php echo e($video['gallery_loc']['gallery_loc']); ?></video:gallery_loc>
                        <?php endif; ?>
                        <?php if(isset($video['price'])): ?>
                            <video:price currency="<?php echo e($video['price']['currency']); ?>"><?php echo e($video['price']['price']); ?>

                            </video:price>
                        <?php endif; ?>
                        <?php if(isset($video['uploader'])): ?>
                            <video:uploader info="<?php echo e($video['uploader']['info']); ?>">
                                <?php echo e($video['uploader']['uploader']); ?></video:uploader>
                        <?php endif; ?>
                    </video:video>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </url>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</urlset>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/packages/sitemap/resources/views/xml.blade.php ENDPATH**/ ?>