@php
    $courseCount = $courses->total();
    $isPaginated = $courses instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
@endphp

<div class="courses-page__container">
    <div class="courses-toolbar" data-course-toolbar data-state="closed">
        <div class="courses-toolbar__bar">
            <p class="courses-toolbar__count" data-course-count aria-live="polite">
                {{ __(':count Kurse verfügbar', ['count' => $courseCount]) }}
            </p>

            <button
                type="button"
                class="courses-toolbar__toggle"
                data-course-toolbar-toggle
                aria-expanded="false"
                aria-controls="courses-toolbar-panel"
            >
                <span class="courses-toolbar__toggle-label">{{ __('Filter & Sortieren') }}</span>
            </button>
        </div>

        {!! Theme::partial('courses.forms.form', [
            'totalCourses' => $courseCount,
            'availableForBooking' => false,
        ]) !!}
    </div>

    @if ($courses->isNotEmpty())
        <div class="courses-grid" data-course-grid>
            @foreach ($courses as $course)
                <div class="courses-grid__item">
                    {!! Theme::partial('courses.item', compact('course')) !!}
                </div>
            @endforeach
        </div>

        @if ($isPaginated)
            <div class="courses-pagination">
                {!! $courses->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')) !!}
            </div>
        @endif
    @else
        <div class="courses-empty" role="status">
            <p>{{ __('Derzeit sind keine Kurse verfügbar') }}</p>
        </div>
    @endif
</div>
