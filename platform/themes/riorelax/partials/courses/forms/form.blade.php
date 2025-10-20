@php
    use Botble\Courses\Models\CourseCategory;
    use Botble\Courses\Models\Instructor;

    $categories = CourseCategory::where('status', 'published')->get();
    $instructors = Instructor::where('status', 'published')->get();

    $selectedCategory = request()->get('category_id');
    $selectedInstructor = request()->get('instructor_id');
    $minPrice = request()->get('min_price');
    $maxPrice = request()->get('max_price');
    $startDate = request()->get('start_date');
@endphp

<form action="{{ route('public.courses') }}" method="GET" class="contact-form course-filter-form">
    <div class="row g-3 g-lg-4 align-items-end course-filter-row">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="contact-field p-relative c-name mb-0">
                <label><i class="fal fa-layer-group"></i> Kategorie </label>
                <select name="category_id" class="form-control">
                    <option value=""> Alle Kategorien </option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected($selectedCategory == $cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="contact-field p-relative c-name mb-0">
                <label><i class="fal fa-chalkboard-teacher"></i> Trainer </label>
                <select name="instructor_id" class="form-control">
                    <option value=""> Alle Trainer </option>
                    @foreach ($instructors as $inst)
                        <option value="{{ $inst->id }}" @selected($selectedInstructor == $inst->id)>
                            {{ $inst->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="contact-field p-relative c-name mb-0">
                <label><i class="fal fa-calendar-alt"></i> Startdatum ab </label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3 d-grid">
            <div class="slider-btn mt-0">
                <button type="submit" class="btn ss-btn" data-animation="fadeInRight" data-delay=".8s">
                    Kurse filtern
                </button>
            </div>
        </div>
    </div>
</form>
