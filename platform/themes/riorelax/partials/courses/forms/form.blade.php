@php
    use Botble\Courses\Models\CourseCategory;
    use Botble\Courses\Models\Instructor;

    $categories = CourseCategory::where('status', 'published')->get();
    $instructors = Instructor::where('status', 'published')->get();

    $selectedCategory = request()->get('category_id');
    $selectedInstructor = request()->get('instructor_id');
    $startDate = request()->get('start_date');
    $sortBy = request()->get('sort_by');
    $sortDirection = request()->get('sort_direction', 'asc');

    $sortValue = match ([$sortBy, $sortDirection]) {
        ['price', 'asc'] => 'price_asc',
        ['price', 'desc'] => 'price_desc',
        ['created_at', 'desc'] => 'created_desc',
        ['created_at', 'asc'] => 'created_asc',
        ['upcoming_session', 'desc'] => 'upcoming_desc',
        default => 'upcoming_asc',
    };
@endphp

<div class="courses-toolbar__overlay" data-course-toolbar-backdrop hidden></div>

<form
    action="{{ route('public.courses') }}"
    method="GET"
    class="courses-toolbar__form"
    data-course-toolbar-panel
    id="courses-toolbar-panel"
>
    <div class="courses-toolbar__filters">
        <div class="courses-toolbar__field">
            <label class="courses-toolbar__label" for="filterKategorie">{{ __('Kategorie') }}</label>
            <select
                name="category_id"
                id="filterKategorie"
                class="courses-toolbar__control"
            >
                <option value="">{{ __('Alle Kategorien') }}</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected($selectedCategory == $cat->id)>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="courses-toolbar__field">
            <label class="courses-toolbar__label" for="filterTrainer">{{ __('Trainer') }}</label>
            <select
                name="instructor_id"
                id="filterTrainer"
                class="courses-toolbar__control"
            >
                <option value="">{{ __('Alle Trainer') }}</option>
                @foreach ($instructors as $inst)
                    <option value="{{ $inst->id }}" @selected($selectedInstructor == $inst->id)>
                        {{ $inst->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="courses-toolbar__field">
            <label class="courses-toolbar__label" for="filterStartAb">{{ __('Startdatum ab') }}</label>
            <input
                type="date"
                name="start_date"
                id="filterStartAb"
                class="courses-toolbar__control"
                value="{{ $startDate }}"
            >
        </div>

        <div class="courses-toolbar__field courses-toolbar__field--compact">
            <label class="courses-toolbar__label" for="filterSortierung">{{ __('Sortieren') }}</label>
            <select
                id="filterSortierung"
                class="courses-toolbar__control"
                data-course-sort-select
                data-initial-sort="{{ $sortValue }}"
            >
                <option value="upcoming_asc">{{ __('Nächstes Startdatum') }}</option>
                <option value="upcoming_desc">{{ __('Spätestes Startdatum') }}</option>
                <option value="created_desc">{{ __('Neueste zuerst') }}</option>
                <option value="created_asc">{{ __('Älteste zuerst') }}</option>
                <option value="price_asc">{{ __('Preis aufsteigend') }}</option>
                <option value="price_desc">{{ __('Preis absteigend') }}</option>
            </select>
        </div>
    </div>

    <div class="courses-toolbar__actions">
        <button type="submit" class="courses-toolbar__submit btn btn-primary">
            {{ __('Kurse filtern') }}
        </button>
        <a
            href="{{ route('public.courses') }}"
            class="courses-toolbar__reset"
            data-course-toolbar-reset
        >
            {{ __('Zurücksetzen') }}
        </a>
    </div>

    <input type="hidden" name="sort_by" value="{{ $sortBy ?? 'upcoming_session' }}" data-course-sort-by>
    <input type="hidden" name="sort_direction" value="{{ $sortDirection }}" data-course-sort-direction>
</form>
