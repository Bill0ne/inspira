<?php $__env->startSection('content'); ?>
    <?php
        $user = auth('customer')->user();
    ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h1 class="text-center"><?php echo e(__('Edit Profile')); ?></h1>
        </div>
        <div>
            <?php echo Form::open(['route' => 'customer.edit-account']); ?>

                <div class="row">
                    <div class="col-md">
                        <div class="form-group mb-20">
                            <label for="first_name" class="input-group-prepend mb-10 mt-20"><?php echo e(__('First Name')); ?>: </label>
                            <input id="first_name" type="text" class="form-control <?php if($errors->has('first_name')): ?> is-invalid <?php endif; ?>" name="first_name" value="<?php echo e($user->first_name); ?>" />
                            <?php echo Form::error('first_name', $errors); ?>

                        </div>
                    </div>
                    <div class="col-md">
                        <div class="form-group mb-20">
                            <label for="last_name" class="input-group-prepend mb-10 mt-20"><?php echo e(__('Last Name')); ?>: </label>
                            <input id="last_name" type="text" class="form-control <?php if($errors->has('last_name')): ?> is-invalid <?php endif; ?>" name="last_name" value="<?php echo e($user->last_name); ?>" />
                            <?php echo Form::error('last_name', $errors); ?>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md">
                        <div class="form-group mb-20">
                            <label for="date_of_birth" class="input-group-prepend mb-10 mt-20"><?php echo e(__('Date of birth')); ?>: </label>
                            <input id="date_of_birth" type="text" class="form-control date-picker <?php if($errors->has('dob')): ?> is-invalid <?php endif; ?>" name="dob" value="<?php echo e($user->dob); ?>" autocomplete="false"/>
                            <?php echo Form::error('dob', $errors); ?>

                        </div>
                    </div>
                    <div class="col-md">
                        <div class="form-group mb-20">
                            <label for="email" class="input-group-prepend mb-10 mt-20"><?php echo e(__('Email')); ?>: </label>
                            <input id="email" type="text" class="form-control <?php if($errors->has('email')): ?> is-invalid <?php endif; ?>" name="email" value="<?php echo e($user->email); ?>" disabled />
                            <?php echo Form::error('email', $errors); ?>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md">
                        <div class="form-group mb-20">
                            <label for="country" class="input-group-prepend mb-10 mt-20"><?php echo e(__('Country')); ?>: </label>
                            <input id="country" type="text" class="form-control <?php if($errors->has('country')): ?> is-invalid <?php endif; ?>" name="country" value="<?php echo e($user->country); ?>" />
                            <?php echo Form::error('country', $errors); ?>

                        </div>
                    </div>
                    <div class="col-md">
                        <div class="form-group mb-20">
                            <label for="state" class="input-group-prepend mb-10 mt-20"><?php echo e(__('State / Province')); ?>: </label>
                            <input id="state" type="text" class="form-control <?php if($errors->has('state')): ?> is-invalid <?php endif; ?>" name="state" value="<?php echo e($user->state); ?>" />
                            <?php echo Form::error('state', $errors); ?>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md">
                        <div class="form-group mb-20">
                            <label for="city" class="input-group-prepend mb-10 mt-20"><?php echo e(__('City')); ?>: </label>
                            <input id="city" type="text" class="form-control <?php if($errors->has('city')): ?> is-invalid <?php endif; ?>" name="city" value="<?php echo e($user->city); ?>" />
                            <?php echo Form::error('city', $errors); ?>

                        </div>
                    </div>
                    <div class="col-md">
                        <div class="form-group mb-20">
                            <label for="address" class="input-group-prepend mb-10 mt-20"><?php echo e(__('Address')); ?>: </label>
                            <input id="address" type="text" class="form-control <?php if($errors->has('address')): ?> is-invalid <?php endif; ?>" name="address" value="<?php echo e($user->address); ?>" />
                            <?php echo Form::error('address', $errors); ?>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md">
                        <div class="form-group mb-20">
                            <label for="zip" class="input-group-prepend mb-10 mt-20"><?php echo e(__('Postal / Zip code')); ?>: </label>
                            <input id="zip" type="text" class="form-control <?php if($errors->has('zip')): ?> is-invalid <?php endif; ?>" name="zip" value="<?php echo e($user->zip); ?>" aria-describedby="txt-zip-error" />
                            <?php echo Form::error('zip', $errors); ?>

                        </div>
                    </div>
                    <div class="col-md">
                        <div class="form-group mb-20">
                            <label for="phone" class="input-group-prepend mb-10 mt-20"><?php echo e(__('Phone number')); ?>: </label>
                            <input id="phone" type="text" class="form-control <?php if($errors->has('phone')): ?> is-invalid <?php endif; ?>" name="phone" value="<?php echo e($user->phone); ?>" />
                            <?php echo Form::error('phone', $errors); ?>

                        </div>
                    </div>
                </div>

                <div class="form-group col s12 mt-20">
                    <button type="submit" class="btn btn-primary customer-btn"><?php echo e(__('Save Changes')); ?></button>
                </div>

            <?php echo Form::close(); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(HotelHelper::viewPath('customers.master'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/views/hotel/customers/edit-account.blade.php ENDPATH**/ ?>