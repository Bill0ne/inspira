<ul <?php echo BaseHelper::clean($options); ?>>
    <?php $__currentLoopData = $menu_nodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="nav-item">
            <a href="<?php echo e($row->url); ?>"
               class="<?php echo \Illuminate\Support\Arr::toCssClasses(['nav-link collapsed', 'has-sub' => $row->has_child, 'active' => $row->active]); ?>"
               target="<?php echo e($row->target); ?>"
               <?php if($row->has_child): ?>
                   data-bs-toggle="collapse"
                   data-bs-target="#menu-collapse-<?php echo e($row->id); ?>"
                   aria-expanded="false"
                   aria-controls="menu-collapse-<?php echo e($row->id); ?>"
               <?php endif; ?>
            ><?php echo e($row->title); ?></a>
        </li>

        <?php if($row->has_child): ?>
            <div class="collapse" id="menu-collapse-<?php echo e($row->id); ?>">
                <?php echo Menu::renderMenuLocation('main-menu', [
                    'menu_nodes' => $row->child,
                    'view' => 'menu-mobile',
                    'options' => ['class' => 'navbar-nav me-auto mb-2 mb-lg-0 ms-3'],
                ]); ?>

            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/partials/menu-mobile.blade.php ENDPATH**/ ?>