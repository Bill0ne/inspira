<ol>
    <li>
        <p>
            <a href="https://dashboard.stripe.com/register" target="_blank">
                <?php echo e(trans('plugins/payment::payment.service_registration', ['name' => 'Stripe'])); ?>

            </a>
        </p>
    </li>
    <li>
        <p><?php echo e(trans('plugins/payment::payment.stripe_after_service_registration_msg', ['name' => 'Stripe'])); ?></p>
    </li>
    <li>
        <p><?php echo e(trans('plugins/payment::payment.stripe_enter_client_id_and_secret')); ?></p>
    </li>
</ol>

<h4><?php echo e(trans('plugins/stripe::stripe.webhook_setup_guide.title')); ?></h4>

<p><?php echo e(trans('plugins/stripe::stripe.webhook_setup_guide.description')); ?></p>

<ol>
    <li>
        <p><strong><?php echo e(trans('plugins/stripe::stripe.webhook_setup_guide.step_1_label')); ?>:</strong> <?php echo BaseHelper::clean(trans('plugins/stripe::stripe.webhook_setup_guide.step_1_description', ['link' => '<a href="https://dashboard.stripe.com/" target="_blank">Stripe Dashboard</a>'])); ?></p>
    </li>

    <li>
        <p><strong><?php echo e(trans('plugins/stripe::stripe.webhook_setup_guide.step_2_label')); ?>:</strong> <?php echo BaseHelper::clean(trans('plugins/stripe::stripe.webhook_setup_guide.step_2_description', ['url' => '<code>' . route('payments.stripe.webhook') . '</code>'])); ?></p>
    </li>

    <li>
        <p><strong><?php echo e(trans('plugins/stripe::stripe.webhook_setup_guide.step_3_label')); ?>:</strong> <?php echo e(trans('plugins/stripe::stripe.webhook_setup_guide.step_3_description')); ?></p>
    </li>

    <li>
        <p><strong><?php echo e(trans('plugins/stripe::stripe.webhook_setup_guide.step_4_label')); ?>:</strong> <?php echo e(trans('plugins/stripe::stripe.webhook_setup_guide.step_4_description')); ?></p>
    </li>
</ol>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/plugins/stripe/resources/views/instructions.blade.php ENDPATH**/ ?>