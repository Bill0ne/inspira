@php
    use Botble\Courses\Models\CourseCategory;
    use Botble\Courses\Models\Instructor;

    $categories = CourseCategory::where('status', 'published')->get();
    $instructors = Instructor::where('status', 'published')->get();

    $selectedCategory = request()->get('category_id');
    $selectedInstructor = request()->get('instructor_id');
    $startDate = request()->get('start_date');

    $isToolbar = ($style ?? null) === 'toolbar';
@endphp

<form action="{{ route('public.courses') }}"
      method="GET"
      @class(['course-filter-form', 'course-filter-toolbar' => $isToolbar])>

    <div class="course-filter-toolbar__inner">
        <div class="course-filter-toolbar__field">
            <label for="course-filter-category">
                <i class="fal fa-layer-group" aria-hidden="true"></i>
                <span>{{ __('Kategorie') }}</span>
            </label>
            <select id="course-filter-category" name="category_id" class="form-control">
                <option value="">{{ __('Alle Kategorien') }}</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected($selectedCategory == $cat->id)>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="course-filter-toolbar__field">
            <label for="course-filter-instructor">
                <i class="fal fa-chalkboard-teacher" aria-hidden="true"></i>
                <span>{{ __('Trainer') }}</span>
            </label>
            <select id="course-filter-instructor" name="instructor_id" class="form-control">
                <option value="">{{ __('Alle Trainer') }}</option>
                @foreach ($instructors as $inst)
                    <option value="{{ $inst->id }}" @selected($selectedInstructor == $inst->id)>
                        {{ $inst->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="course-filter-toolbar__field">
            <label for="course-filter-start-date">
                <i class="fal fa-calendar-alt" aria-hidden="true"></i>
                <span>{{ __('Startdatum ab') }}</span>
            </label>
            <input id="course-filter-start-date"
                   type="date"
                   name="start_date"
                   class="form-control"
                   value="{{ $startDate }}">
        </div>

        <div class="course-filter-toolbar__actions">
            <button type="submit" class="btn ss-btn course-filter-toolbar__submit" data-animation="fadeInRight" data-delay=".8s">
                {{ __('Filter anwenden') }}
            </button>
        </div>
    </div>
</form>

@if ($isToolbar)
    @push('styles')
        <style>
            .course-filter-toolbar {
                background: #ffffff;
                border-radius: 24px;
                padding: 1.75rem 2rem;
                box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08);
            }

            .course-filter-toolbar__inner {
                display: flex;
                flex-wrap: wrap;
                align-items: flex-end;
                gap: 1.25rem 2rem;
            }

            .course-filter-toolbar__field {
                flex: 1 1 220px;
                min-width: 200px;
            }

            .course-filter-toolbar__field label {
                display: flex;
                align-items: center;
                gap: .5rem;
                font-size: .875rem;
                font-weight: 600;
                color: #0f172a;
                margin-bottom: .5rem;
            }

            .course-filter-toolbar__field .form-control {
                border-radius: 12px;
                border: 1px solid #e2e8f0;
                padding: .65rem .85rem;
                font-size: .95rem;
                transition: border-color .2s ease, box-shadow .2s ease;
            }

            .course-filter-toolbar__field .form-control:focus {
                border-color: #7c3aed;
                box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.12);
            }

            .course-filter-toolbar__actions {
                flex: 0 0 auto;
                display: flex;
                justify-content: flex-end;
                min-width: 180px;
            }

            .course-filter-toolbar__submit {
                width: 100%;
                padding: .85rem 1.8rem;
                border-radius: 14px;
                font-weight: 600;
                letter-spacing: .01em;
            }

            @media (max-width: 991.98px) {
                .course-filter-toolbar {
                    padding: 1.5rem;
                }

                .course-filter-toolbar__inner {
                    gap: 1rem 1.25rem;
                }
            }

            @media (max-width: 575.98px) {
                .course-filter-toolbar {
                    padding: 1.25rem;
                    border-radius: 18px;
                }

                .course-filter-toolbar__field,
                .course-filter-toolbar__actions {
                    flex: 1 1 100%;
                    min-width: 100%;
                }

                .course-filter-toolbar__submit {
                    width: 100%;
                }
            }
        </style>
    @endpush
@endif
