<?php $__env->startSection('content'); ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h1 class="customer-page-title text-center"><?php echo e(__('Change password')); ?></h1>
        </div>
        <div class="panel-body custom-edit-account-form">
            <?php echo Form::open(['route' => 'customer.post.change-password', 'method' => 'post']); ?>


            <div class="row">
                <div class="col-md-12">
                    <div class="form-group mb-20">
                        <label for="old_password" class="input-group-prepend mb-10 mt-20"><?php echo e(__('Old Password')); ?>: </label>
                        <input id="old_password" type="password" class="form-control <?php if($errors->has('old_password')): ?> is-invalid <?php endif; ?>" name="old_password" placeholder="<?php echo e(__('Current Password')); ?>" />
                        <?php echo Form::error('old_password', $errors); ?>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group mb-20">
                        <label for="password" class="input-group-prepend mb-10 mt-20"><?php echo e(__('New Password')); ?>: </label>
                        <input id="password" type="password" class="form-control <?php if($errors->has('old_password')): ?> is-invalid <?php endif; ?>" name="password" placeholder="<?php echo e(__('New Password')); ?>" />
                        <?php echo Form::error('password', $errors); ?>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group mb-20">
                        <label for="password_confirmation" class="input-group-prepend mb-10 mt-20"><?php echo e(__('Password Confirmation')); ?>: </label>
                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" placeholder="<?php echo e(__('Password Confirmation')); ?>" />
                    </div>
                </div>
            </div>

            <div class="form-group col s12 mt-20">
                <button type="submit" class="btn btn-primary btn-sm"><?php echo e(__('Change password')); ?></button>
            </div>

            <?php echo Form::close(); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(HotelHelper::viewPath('customers.master'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/views/hotel/customers/change-password.blade.php ENDPATH**/ ?>