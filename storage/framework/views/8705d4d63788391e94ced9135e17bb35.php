<?php if(is_plugin_active('hotel') && count($currencies = get_all_currencies()) > 0): ?>
    <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li>
            <a class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => $currency->id === get_application_currency_id()]); ?>" class="currency-item" href="<?php echo e(route('public.change-currency', $currency->title)); ?>"><?php echo e($currency->title); ?></a>
        </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/partials/currency-switcher-mobile.blade.php ENDPATH**/ ?>