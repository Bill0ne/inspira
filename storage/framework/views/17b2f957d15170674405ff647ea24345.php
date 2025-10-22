<?php
    Theme::asset()->container('footer')->usePath()->add('imagesloaded', 'plugins/imagesloaded.min.js', ['jquery']);
    Theme::layout('full-width');
    Theme::set('pageTitle', 'Galleries');
    Theme::set('breadcrumb', true);
?>

<section class="section pb-100">
    <section class="profile fix pt-60">
        <div class="container-xxl">
            <?php echo Theme::partial('gallery.galleries', ['galleries' => $galleries]); ?>

        </div>
    </section>
</section>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/views/galleries.blade.php ENDPATH**/ ?>