<?php if($payment): ?>
    <div
        class="alert alert-success"
        role="alert"
    >
        <p class="mb-2"><?php echo e(trans('plugins/payment::payment.payment_id')); ?>: <strong><?php echo e($payment->id); ?></strong></p>
        <p class="mb-2"><?php echo e(trans('plugins/payment::payment.payer_name')); ?>: <?php echo e($payment->billing_details->name); ?></p>
        <?php if($payment->source): ?>
            <p class="mb-2"><?php echo e(trans('plugins/payment::payment.card')); ?>: <?php echo e($payment->source->brand); ?> - **** ****
                **** <?php echo e($payment->source->last4); ?>

                - <?php echo e($payment->source->exp_month); ?>/<?php echo e($payment->source->exp_year); ?></p>
        <?php endif; ?>

        <?php if($payment->billing_details->country): ?>
            <p class="<?php if(!empty($payment->billing_details->address_line1)): ?> mb-2 <?php else: ?> mb-0 <?php endif; ?>">
                <?php echo e(trans('plugins/payment::payment.country')); ?>: <?php echo e($payment->billing_details->country); ?></p>
        <?php endif; ?>

        <?php if(!empty($payment->billing_details->address_line1)): ?>
            <p class="mb-0"><?php echo e(trans('plugins/payment::payment.address')); ?>:
                <?php echo e($payment->billing_details->address_line1); ?></p>
        <?php endif; ?>

    </div>

    <?php if($payment->refunds && $payment->refunds->total_count): ?>
        <br />
        <h6 class="alert-heading">
            <?php echo e(trans('plugins/payment::payment.refunds.title') . ' (' . $payment->refunds->total_count . ')'); ?></h6>
        <hr class="m-0 mb-4">
        <?php $__currentLoopData = $payment->refunds->data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div
                class="alert alert-warning"
                role="alert"
            >
                <p><?php echo e(trans('plugins/payment::payment.refunds.id')); ?>: <?php echo e($item->id); ?></p>
                <?php
                    $multiplier = Botble\Stripe\Supports\StripeHelper::getStripeCurrencyMultiplier($item->currency);

                    if ($multiplier > 1) {
                        $item->amount = round($item->amount / $multiplier, 2);
                    }
                ?>
                <p><?php echo e(trans('plugins/payment::payment.amount')); ?>: <?php echo e($item->amount); ?>

                    <?php echo e(strtoupper($item->currency)); ?></p>
                <p><?php echo e(trans('plugins/payment::payment.refunds.status')); ?>: <?php echo e(strtoupper($item->status)); ?></p>
                <p><?php echo e(trans('plugins/payment::payment.refunds.create_time')); ?>:
                    <?php echo e(BaseHelper::formatDate($item->created)); ?></p>
            </div>
            <br />
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    <?php echo $__env->make('plugins/payment::partials.view-payment-source', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/plugins/stripe/resources/views/detail.blade.php ENDPATH**/ ?>