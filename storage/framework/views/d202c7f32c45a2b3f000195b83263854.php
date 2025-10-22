<ul <?php echo BaseHelper::clean($options); ?>>
    <?php $__currentLoopData = $menu_nodes->loadMissing('metadata'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li>
            <a target="<?php echo e($item->target); ?>" class="font-sm color-grey-200" href="<?php echo e(url($item->url)); ?>"><?php echo e($item->title); ?></a>
        </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/partials/footer-menu.blade.php ENDPATH**/ ?>