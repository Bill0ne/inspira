<?php if (isset($component)) { $__componentOriginal69057bacd9705b1c669802ff37556f6e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal69057bacd9705b1c669802ff37556f6e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'fa081de1c3ee47622336b4eeafa01705::payment-method','data' => ['name' => \Botble\Payment\Enums\PaymentMethodEnum::COD,'label' => get_payment_setting('name', 'cod', trans('plugins/payment::payment.payment_via_cod'))]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('plugins-payment::payment-method'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Botble\Payment\Enums\PaymentMethodEnum::COD),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(get_payment_setting('name', 'cod', trans('plugins/payment::payment.payment_via_cod')))]); ?>
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
<?php /**PATH D:\xampp\htdocs\inspira\platform/plugins/payment/resources/views/partials/cod.blade.php ENDPATH**/ ?>