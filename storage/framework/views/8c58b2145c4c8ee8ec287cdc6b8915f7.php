<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1" name="viewport" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <link href="https://fonts.googleapis.com/css?family=<?php echo e(urlencode(theme_option('primary_font', 'Epilogue'))); ?>:400,500,600,700" rel="stylesheet" type="text/css">

    <style>
        :root {
            --primary-color: <?php echo e(theme_option('primary_color', '#fec201')); ?>;
            --secondary-color: <?php echo e(theme_option('secondary_color', '#034460')); ?>;
            --input-border-color: <?php echo e(theme_option('input_border_color', '#d7cfc8')); ?>;
            --primary-color-hover: <?php echo e(theme_option('primary_color_hover', '#066a4c')); ?>;
            --btn-text-color-hover: <?php echo e(theme_option('button_text_color_hover', '#101010')); ?>;
            --heading-font: '<?php echo e(theme_option('heading_font', 'Jost')); ?>', sans-serif;
            --primary-font: '<?php echo e(theme_option('primary_font', 'Roboto')); ?>', sans-serif;
        }
    </style>
    <?php echo Theme::header(); ?>

    <?php echo Theme::partial('preloader'); ?>

</head>
<body <?php if(BaseHelper::isRtlEnabled()): ?> dir="rtl" <?php endif; ?>>
<?php echo apply_filters(THEME_FRONT_BODY, null); ?>


<?php echo $__env->yieldContent('main'); ?>

<?php echo Theme::footer(); ?>

<?php if(session()->has('success_msg') || session()->has('error_msg') || (isset($errors) && $errors->count() > 0) || isset($error_msg)): ?>
    <script type="text/javascript">
        $(document).ready(function () {
            <?php if(session()->has('success_msg')): ?>
                RiorelaxTheme.showSuccess('<?php echo e(session('success_msg')); ?>');
            <?php endif; ?>

            <?php if(session()->has('error_msg')): ?>
                RiorelaxTheme.showError('<?php echo e(session('error_msg')); ?>');
            <?php endif; ?>

            <?php if(isset($error_msg)): ?>
                RiorelaxTheme.showError('<?php echo e($error_msg); ?>');
            <?php endif; ?>

            <?php if(isset($errors)): ?>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    RiorelaxTheme.showError('<?php echo BaseHelper::clean($error); ?>');
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        });
    </script>
<?php endif; ?>
</body>
</html>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/layouts/base.blade.php ENDPATH**/ ?>