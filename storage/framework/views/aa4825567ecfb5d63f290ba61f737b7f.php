<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'label' => null,
    'description' => null,
    'paymentName' => null,
    'supportedCurrencies' => [],
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'name',
    'label' => null,
    'description' => null,
    'paymentName' => null,
    'supportedCurrencies' => [],
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $isSelected = PaymentMethods::getSelectingMethod() === $name;
    $id = sprintf('payment-%s', $name);
?>

<li class="list-group-item payment-method-item">
    <input
        class="magic-radio js_payment_method"
        id="<?php echo e($id); ?>"
        name="payment_method"
        type="radio"
        value="<?php echo e($name); ?>"
        <?php if($isSelected): echo 'checked'; endif; ?>
    >
    <label for="<?php echo e($id); ?>" class="form-label fw-medium">
        <?php echo e($paymentLabel = ($label ?: get_payment_setting('name', $name) ?: setting('payment_' . $name . '_name') ?: $paymentName)); ?>

    </label>

    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['payment_collapse_wrap collapse mt-1', 'show' => $isSelected]); ?>">
        <p class="text-muted"><?php echo BaseHelper::clean($description ?: get_payment_setting('description', $name) ?: setting('payment_' . $name . '_description')); ?></p>
        <?php
            $feeValue = get_payment_setting('fee', $name, 0);
            $feeType = get_payment_setting('fee_type', $name, \Botble\Payment\Enums\PaymentFeeTypeEnum::FIXED);
            $orderAmount = apply_filters('payment_order_total_amount', 0);
            $fee = \Botble\Payment\Supports\PaymentFeeHelper::calculateFee($name, $orderAmount);
        ?>
        <?php if($feeValue > 0): ?>
            <p class="text-warning">
                <?php if($feeType === \Botble\Payment\Enums\PaymentFeeTypeEnum::PERCENTAGE): ?>
                    <?php echo e(__('plugins/payment::payment.payment_fee')); ?>: <?php echo e(format_price($fee)); ?> (<?php echo e($feeValue); ?>%)
                <?php else: ?>
                    <?php echo e(__('plugins/payment::payment.payment_fee')); ?>: <?php echo e(format_price($fee)); ?>

                <?php endif; ?>
                <input type="hidden" name="payment_fee" value="<?php echo e($fee); ?>" class="payment-fee-input" data-method="<?php echo e($name); ?>">
            </p>
        <?php endif; ?>

        <?php echo e($slot); ?>


        <?php echo apply_filters('payment_method_display_body', null, $name, $paymentLabel); ?>


        <?php if(
            ! empty($supportedCurrencies)
            && ! in_array(get_application_currency()->title, $supportedCurrencies)
            && ! get_application_currency()->replicate()->newQuery()->whereIn('title', $supportedCurrencies)->exists()
        ): ?>
            <?php
                $currencies = get_all_currencies()->filter(fn ($item) => in_array($item->title, $supportedCurrencies));
            ?>

            <div class="alert alert-warning mt-3">
                <?php echo e(__(":name doesn't support :currency. List of currencies supported by :name: :currencies.", ['name' => $paymentName, 'currency' => get_application_currency()->title, 'currencies' => implode(', ', $supportedCurrencies)])); ?>


                <?php echo e($currencyNotSupportedMessage ?? ''); ?>


                <?php if($currencies->isNotEmpty()): ?>
                    <div>
                        <?php echo e(__('Please switch currency to any supported currency')); ?>:&nbsp;&nbsp;
                        <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a
                                href="<?php echo e(route('public.change-currency', $currency->title)); ?>"
                                class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => get_application_currency_id() === $currency->getKey()]); ?>"
                            >
                                <?php echo e($currency->title); ?>

                            </a>
                            <?php if(!$loop->last): ?>
                                &nbsp; | &nbsp;
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if($logo = get_payment_setting('logo', $name)): ?>
        <div class="payment-method-logo">
            <?php echo e(RvMedia::image($logo)); ?>

        </div>
    <?php endif; ?>
</li>
<?php /**PATH D:\xampp\htdocs\inspira\platform/plugins/payment/resources/views/components/payment-method.blade.php ENDPATH**/ ?>