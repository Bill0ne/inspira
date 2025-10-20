<?php if(setting('payment_paypal_status') == 1): ?>
    <?php if (isset($component)) { $__componentOriginal69057bacd9705b1c669802ff37556f6e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal69057bacd9705b1c669802ff37556f6e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'fa081de1c3ee47622336b4eeafa01705::payment-method','data' => ['name' => PAYPAL_PAYMENT_METHOD_NAME,'paymentName' => 'PayPal','supportedCurrencies' => (new Botble\PayPal\Services\Gateways\PayPalPaymentService)->supportedCurrencyCodes()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('plugins-payment::payment-method'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(PAYPAL_PAYMENT_METHOD_NAME),'paymentName' => 'PayPal','supportedCurrencies' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((new Botble\PayPal\Services\Gateways\PayPalPaymentService)->supportedCurrencyCodes())]); ?>
         <?php $__env->slot('currencyNotSupportedMessage', null, []); ?> 
            <p class="mt-1 mb-0">
                <?php echo e(__('Learn more')); ?>:
                <?php echo e(Html::link('https://developer.paypal.com/docs/api/reference/currency-codes', attributes: ['target' => '_blank', 'rel' => 'nofollow'])); ?>.
            </p>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal69057bacd9705b1c669802ff37556f6e)): ?>
<?php $attributes = $__attributesOriginal69057bacd9705b1c669802ff37556f6e; ?>
<?php unset($__attributesOriginal69057bacd9705b1c669802ff37556f6e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal69057bacd9705b1c669802ff37556f6e)): ?>
<?php $component = $__componentOriginal69057bacd9705b1c669802ff37556f6e; ?>
<?php unset($__componentOriginal69057bacd9705b1c669802ff37556f6e); ?>
<?php endif; ?>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/plugins/paypal/resources/views/methods.blade.php ENDPATH**/ ?>