<?php if(setting('payment_stripe_status') == 1): ?>
    <?php if (isset($component)) { $__componentOriginal69057bacd9705b1c669802ff37556f6e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal69057bacd9705b1c669802ff37556f6e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'fa081de1c3ee47622336b4eeafa01705::payment-method','data' => ['name' => STRIPE_PAYMENT_METHOD_NAME,'paymentName' => 'Stripe','supportedCurrencies' => (new Botble\Stripe\Services\Gateways\StripePaymentService)->supportedCurrencyCodes()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('plugins-payment::payment-method'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(STRIPE_PAYMENT_METHOD_NAME),'paymentName' => 'Stripe','supportedCurrencies' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((new Botble\Stripe\Services\Gateways\StripePaymentService)->supportedCurrencyCodes())]); ?>
        <?php if(get_payment_setting('payment_type', STRIPE_PAYMENT_METHOD_NAME, 'stripe_api_charge') == 'stripe_api_charge'): ?>
            <div class="card-checkout" style="max-width: 350px">
                <div class="form-group mt-3 mb-3">
                    <div class="stripe-card-wrapper"></div>
                </div>

                <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['form-group mb-3', 'has-error' => $errors->has('number') || $errors->has('expiry')]); ?>">
                    <div class="row">
                        <div class="col-sm-8">
                            <input
                                class="form-control"
                                id="stripe-number"
                                data-stripe="number"
                                type="text"
                                placeholder="<?php echo e(trans('plugins/payment::payment.card_number')); ?>"
                                autocomplete="off"
                            >
                        </div>
                        <div class="col-sm-4">
                            <input
                                class="form-control"
                                id="stripe-exp"
                                data-stripe="exp"
                                type="text"
                                placeholder="<?php echo e(trans('plugins/payment::payment.mm_yy')); ?>"
                                autocomplete="off"
                            >
                        </div>
                    </div>
                </div>
                <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['form-group mb-3', 'has-error' => $errors->has('name') || $errors->has('cvc')]); ?>">
                    <div class="row">
                        <div class="col-sm-8">
                            <input
                                class="form-control"
                                id="stripe-name"
                                data-stripe="name"
                                type="text"
                                placeholder="<?php echo e(trans('plugins/payment::payment.full_name')); ?>"
                                autocomplete="off"
                            >
                        </div>
                        <div class="col-sm-4">
                            <input
                                class="form-control"
                                id="stripe-cvc"
                                data-stripe="cvc"
                                type="text"
                                placeholder="<?php echo e(trans('plugins/payment::payment.cvc')); ?>"
                                autocomplete="off"
                            >
                        </div>
                    </div>
                </div>
            </div>
            <div id="payment-stripe-key" data-value="<?php echo e(get_payment_setting('client_id', STRIPE_PAYMENT_METHOD_NAME)); ?>"></div>
        <?php endif; ?>
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
<?php /**PATH D:\xampp\htdocs\inspira\platform/plugins/stripe/resources/views/methods.blade.php ENDPATH**/ ?>