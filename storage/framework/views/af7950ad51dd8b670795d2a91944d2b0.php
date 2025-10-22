<ul <?php echo BaseHelper::clean($options); ?>>
    <?php $__currentLoopData = $menu_nodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="<?php echo \Illuminate\Support\Arr::toCssClasses(['has-sub' => $row->has_child, $row->css_class]); ?>">
            <a class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => $row->active]); ?>" href="<?php echo e($row->url); ?>" target="<?php echo e($row->target); ?>">
                <?php if($iconImage = $row->getMetaData('icon_image', true)): ?>
                    <img src="<?php echo e(RvMedia::getImageUrl($iconImage)); ?>" alt="<?php echo e($row->title); ?>" loading="lazy"/>
                <?php elseif($row->icon_font): ?>
                    <i class="<?php echo e(trim($row->icon_font)); ?>"></i>
                <?php endif; ?>

                <?php echo e($row->title); ?>

            </a>
            <?php if($row->has_child): ?>
                <?php echo Menu::renderMenuLocation('main-menu', [
                    'menu_nodes' => $row->child,
                    'view' => 'main-menu',
                    'options' => ['class' => 'sub-menu'],
                ]); ?>

            <?php endif; ?>
        </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/partials/main-menu.blade.php ENDPATH**/ ?>