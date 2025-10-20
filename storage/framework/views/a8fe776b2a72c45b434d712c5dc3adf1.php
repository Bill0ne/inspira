<div
    class="debug-badge"
    role="button"
    data-bs-toggle="modal"
    data-bs-target="#debug-mode-modal"
>Debug Mode</div>

<?php if (isset($component)) { $__componentOriginal9376784f974ff66f3ff18195ab0a89c5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9376784f974ff66f3ff18195ab0a89c5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::modal.action','data' => ['id' => 'debug-mode-modal','type' => 'info','title' => 'Debug Mode','size' => 'md','submitButtonLabel' => __('Fix it for me'),'submitButtonAttrs' => ['data-bs-toggle' => 'modal', 'data-bs-target' => '#debug-mode-turn-off-confirmation-modal'],'submitButtonColor' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::modal.action'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'debug-mode-modal','type' => 'info','title' => 'Debug Mode','size' => 'md','submit-button-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Fix it for me')),'submit-button-attrs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['data-bs-toggle' => 'modal', 'data-bs-target' => '#debug-mode-turn-off-confirmation-modal']),'submit-button-color' => 'warning']); ?>
    <div class="text-start">
        <p>
            By default, this option is set to respect the value of the <code class="text-danger">APP_DEBUG</code>
            environment variable, which is stored in your <code class="text-danger">.env</code> file.
        </p>
        <p>
            For local development, you should set the <code class="text-danger">APP_DEBUG</code> environment variable to
            <code class="text-danger">true</code>. In your production environment, this value should always be <code
                class="text-danger"
            >false</code>. If the variable is set to <code class="text-danger">true</code> in production, you risk
            exposing sensitive configuration values to your application's end users.
        </p>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9376784f974ff66f3ff18195ab0a89c5)): ?>
<?php $attributes = $__attributesOriginal9376784f974ff66f3ff18195ab0a89c5; ?>
<?php unset($__attributesOriginal9376784f974ff66f3ff18195ab0a89c5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9376784f974ff66f3ff18195ab0a89c5)): ?>
<?php $component = $__componentOriginal9376784f974ff66f3ff18195ab0a89c5; ?>
<?php unset($__componentOriginal9376784f974ff66f3ff18195ab0a89c5); ?>
<?php endif; ?>

<?php if (isset($component)) { $__componentOriginal9376784f974ff66f3ff18195ab0a89c5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9376784f974ff66f3ff18195ab0a89c5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8def1252668913628243c4d363bee1ef::modal.action','data' => ['id' => 'debug-mode-turn-off-confirmation-modal','type' => 'warning','title' => __('Are you sure?'),'description' => __('Are you sure you want to turn off the debug mode? This action cannot be undone.'),'submitButtonLabel' => __('Yes, turn off'),'submitButtonAttrs' => ['id' => 'debug-mode-turn-off-form-submit', 'data-url' => route('system.debug-mode.turn-off')],'cancelButton' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('core::modal.action'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'debug-mode-turn-off-confirmation-modal','type' => 'warning','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Are you sure?')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Are you sure you want to turn off the debug mode? This action cannot be undone.')),'submit-button-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Yes, turn off')),'submit-button-attrs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['id' => 'debug-mode-turn-off-form-submit', 'data-url' => route('system.debug-mode.turn-off')]),'cancel-button' => true]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9376784f974ff66f3ff18195ab0a89c5)): ?>
<?php $attributes = $__attributesOriginal9376784f974ff66f3ff18195ab0a89c5; ?>
<?php unset($__attributesOriginal9376784f974ff66f3ff18195ab0a89c5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9376784f974ff66f3ff18195ab0a89c5)): ?>
<?php $component = $__componentOriginal9376784f974ff66f3ff18195ab0a89c5; ?>
<?php unset($__componentOriginal9376784f974ff66f3ff18195ab0a89c5); ?>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/core/base/resources/views/components/debug-badge.blade.php ENDPATH**/ ?>