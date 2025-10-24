<?php
    use Botble\Courses\Models\CourseCategory;
    use Botble\Courses\Models\Instructor;

    $categories = CourseCategory::where('status', 'published')->get();
    $instructors = Instructor::where('status', 'published')->get();

    $selectedCategory = request()->get('category_id');
    $selectedInstructor = request()->get('instructor_id');
    $minPrice = request()->get('min_price');
    $maxPrice = request()->get('max_price');
    $startDate = request()->get('start_date');
?>

<form action="<?php echo e(route('public.courses')); ?>" method="GET" class="contact-form mt-30 course-filter-form">

    <div class="contact-field p-relative c-name mb-20">
        <label><i class="fal fa-layer-group"></i> Kategorie </label>
        <select name="category_id" class="form-control">
            <option value=""> Alle Kategorien </option>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($cat->id); ?>" <?php if($selectedCategory == $cat->id): echo 'selected'; endif; ?>>
                    <?php echo e($cat->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="contact-field p-relative c-name mb-20">
        <label><i class="fal fa-chalkboard-teacher"></i> Trainer </label>
        <select name="instructor_id" class="form-control">
            <option value=""> Alle Trainer </option>
            <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($inst->id); ?>" <?php if($selectedInstructor == $inst->id): echo 'selected'; endif; ?>>
                    <?php echo e($inst->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="contact-field p-relative c-name mb-20">
        <label><i class="fal fa-calendar-alt"></i> Startdatum ab </label>
        <input type="date" name="start_date" class="form-control" value="<?php echo e($startDate); ?>">
    </div>

    <div class="slider-btn mt-15">
        <button type="submit" class="btn ss-btn w-100" data-animation="fadeInRight" data-delay=".8s">
            Kurse filtern
        </button>
    </div>
</form>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/partials/courses/forms/form.blade.php ENDPATH**/ ?>