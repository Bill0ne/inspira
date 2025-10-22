<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">Mengen- & Spezialrabatte</h4>
            <a href="<?php echo e(route('pc.discounts.create')); ?>" class="btn btn-primary">Neu</a>
        </div>
        <div class="card-body table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Titel</th>
                        <th>Bedingung</th>
                        <th>Bereich</th>
                        <th>Typ</th>
                        <th>Wert</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($i->title); ?></td>
                        <td><?php echo e(strtoupper($i->condition_type)); ?></td>
                        <td><?php echo e($i->range_min ?? '–'); ?> – <?php echo e($i->range_max ?? '∞'); ?></td>
                        <td><?php echo e(strtoupper($i->discount_type)); ?></td>
                        <td><?php echo e($i->discount_value); ?></td>
                        <td><?php echo e($i->priority); ?></td>
                        <td>
                            <span class="badge <?php echo e($i->status === 'active' ? 'bg-success' : 'bg-secondary'); ?>">
                                <?php echo e($i->status); ?>

                            </span>
                        </td>
                        <td class="d-flex gap-2">
                            <a href="<?php echo e(route('pc.discounts.edit', $i->id)); ?>" class="btn btn-sm btn-warning">Bearbeiten</a>
                            <form action="<?php echo e(route('pc.discounts.toggle', $i->id)); ?>" method="POST" style="display:inline-block">
                                <?php echo csrf_field(); ?>
                                <button class="btn btn-sm btn-info" type="submit">Toggle</button>
                            </form>
                            <form action="<?php echo e(route('pc.discounts.destroy', $i->id)); ?>" method="POST" style="display:inline-block" onsubmit="return confirm('Löschen?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-sm btn-danger" type="submit">Löschen</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" class="text-center">Keine Einträge</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            <?php echo e($items->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(BaseHelper::getAdminMasterLayoutTemplate(), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/plugins/price-configurator/resources/views/discounts/index.blade.php ENDPATH**/ ?>