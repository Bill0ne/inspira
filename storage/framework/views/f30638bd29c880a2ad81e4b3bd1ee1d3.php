<?php $__env->startSection('content'); ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h1 class="text-center"><?php echo e(__('Account information')); ?></h1>
        </div>

        <div class="mt-30">
            <div class="row">
                <div class="col-md-6">
                    <?php if(auth('customer')->user()->name): ?>
                        <p>
                            <strong>
                                <?php echo e(__('Name')); ?>

                            </strong>:
                            <i><?php echo e(auth('customer')->user()->name); ?></i>
                        </p>
                    <?php endif; ?>

                    <?php if(auth('customer')->user()->email): ?>
                        <p>
                            <strong>
                                <?php echo e(__('Email')); ?>

                            </strong>:
                            <i><?php echo e(auth('customer')->user()->email); ?></i>
                        </p>
                    <?php endif; ?>

                    <?php if(auth('customer')->user()->country): ?>
                        <p>
                            <strong>
                                <?php echo e(__('Country')); ?>

                            </strong>:
                            <i><?php echo e(auth('customer')->user()->country); ?></i>
                        </p>
                    <?php endif; ?>

                    <?php if(auth('customer')->user()->city): ?>
                        <p>
                            <strong>
                                <?php echo e(__('City')); ?>

                            </strong>:
                            <i><?php echo e(auth('customer')->user()->city); ?></i>
                        </p>
                    <?php endif; ?>

                    <?php if(auth('customer')->user()->zip): ?>
                        <p>
                            <strong>
                                <?php echo e(__('Postal / Zip code')); ?>

                            </strong>:
                            <i><?php echo e(auth('customer')->user()->zip); ?></i>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <?php if(auth('customer')->user()->dob): ?>
                        <p>
                            <strong>
                                <?php echo e(__('Date of birth')); ?>

                            </strong>:
                            <i><?php echo e(auth('customer')->user()->dob); ?></i>
                        </p>
                    <?php endif; ?>

                    <?php if(auth('customer')->user()->phone): ?>
                        <p>
                            <strong>
                                <?php echo e(__('Phone')); ?>

                            </strong>:
                            <i><?php echo e(auth('customer')->user()->phone); ?></i>
                        </p>
                    <?php endif; ?>

                    <?php if(auth('customer')->user()->state): ?>
                        <p>
                            <strong>
                                <?php echo e(__('State / Province')); ?>

                            </strong>:
                            <i><?php echo e(auth('customer')->user()->state); ?></i>
                        </p>
                    <?php endif; ?>

                    <?php if(auth('customer')->user()->address): ?>
                        <p>
                            <strong>
                                <?php echo e(__('Address')); ?>

                            </strong>:
                            <i><?php echo e(auth('customer')->user()->address); ?></i>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(HotelHelper::viewPath('customers.master'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/views/hotel/customers/overview.blade.php ENDPATH**/ ?>