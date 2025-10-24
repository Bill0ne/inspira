<div class="col-xl-2 col-lg-2 col-sm-6">
    <div class="footer-widget mb-30">
        <div class="f-widget-title">
            <h2><?php echo e($config['name']); ?></h2>
        </div>
        <div class="footer-link">
            <?php echo Menu::generateMenu([
               'slug' => Arr::get($config, 'menu_id'),
               'view' => 'footer-menu',
           ]); ?>

        </div>
    </div>
</div>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/////widgets/custom-menu/templates/frontend.blade.php ENDPATH**/ ?>