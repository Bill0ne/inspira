<?php (Theme::set('pageTitle', __('Rooms'))); ?>

<section class="container">
    <div class="row">
        <div class="col-lg-8">
            <?php echo do_shortcode('[all-rooms]'); ?>

        </div>
        <div class="col-lg-4">
            <div class="sidebar-widget-rooms">
                <?php echo dynamic_sidebar('rooms_sidebar'); ?>

            </div>
        </div>
    </div>
</section>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/views/hotel/rooms.blade.php ENDPATH**/ ?>