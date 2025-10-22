<?php $__env->startSection('content'); ?>
    <div class="max-w-full">
        <?php if(session('status')): ?>
            <div class="alert alert-success"><?php echo e(session('status')); ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <a href="<?php echo e(route('pc.tiers.create')); ?>" class="btn btn-primary">Neue Stufe</a>
        </div>

        <div class="card">
            <div class="card-header"><h5>Preisstufen</h5></div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead><tr><th>ID</th><th>Name</th><th>Prio</th><th>Status</th><th>Exklusiv</th><th>Zeitraum</th><th>Aktionen</th></tr></thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($it->id); ?></td>
                                <td><?php echo e($it->name); ?></td>
                                <td><?php echo e($it->priority); ?></td>
                                <td>
                                    <form method="POST" action="<?php echo e(route('pc.tiers.toggle', $it->id)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn btn-xs <?php echo e($it->status === 'active' ? 'btn-success' : 'btn-warning'); ?>">
                                            <?php echo e($it->status); ?>

                                        </button>
                                    </form>
                                </td>
                                <td><?php echo e($it->is_exclusive ? 'ja' : 'nein'); ?></td>
                                <td><?php echo e($it->starts_at ?? '-'); ?> – <?php echo e($it->ends_at ?? '-'); ?></td>
                                <td>
                                    <a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('pc.tiers.edit', $it->id)); ?>">Bearbeiten</a>
                                    <form method="POST" action="<?php echo e(route('pc.tiers.destroy', $it->id)); ?>" style="display:inline-block" onsubmit="return confirm('Löschen? Regeln werden mitgelöscht!')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-sm btn-outline-danger">Löschen</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="7">Keine Stufen vorhanden.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="p-3"><?php echo e($items->links()); ?></div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core/base::layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/plugins/price-configurator/resources/views/tiers/index.blade.php ENDPATH**/ ?>