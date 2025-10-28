<section class="courses-catalog">
    <div class="courses-catalog__header">
        <h3 class="courses-catalog__count">
            {{ __(':count Kurse verfügbar', ['count' => $courses->total()]) }}
        </h3>
        @if(request()->hasAny(['category_id', 'instructor_id', 'start_date']) && $courses->total())
            <a href="{{ route('public.courses') }}" class="courses-catalog__reset">
                <i class="fal fa-sync"></i>
                <span>{{ __('Filter zurücksetzen') }}</span>
            </a>
        @endif
    </div>

    @if ($courses->isNotEmpty())
        <div class="row g-4 courses-catalog__grid">
            @foreach ($courses as $course)
                <div class="col-sm-6 col-xl-3">
                    {!! Theme::partial('courses.item', compact('course')) !!}
                </div>
            @endforeach
        </div>

        @if ($courses instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
            <div class="text-center mt-40">
                {!! $courses->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')) !!}
            </div>
        @endif
    @else
        <p class="courses-catalog__empty">{{ __('Derzeit sind keine Kurse verfügbar') }}</p>
    @endif
</section>

@push('styles')
    <style>
        .courses-catalog__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.75rem;
        }

        .courses-catalog__count {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: #111827;
        }

        .courses-catalog__reset {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            font-size: .875rem;
            font-weight: 500;
            color: #7c3aed;
            text-decoration: none;
            transition: color .2s ease;
        }

        .courses-catalog__reset:hover,
        .courses-catalog__reset:focus {
            color: #5b21b6;
        }

        .courses-catalog__empty {
            margin: 0;
            padding: 2.5rem 0;
            text-align: center;
            color: #6b7280;
            font-size: 1rem;
        }

        @media (max-width: 575.98px) {
            .courses-catalog__header {
                flex-direction: column;
                align-items: flex-start;
            }

            .courses-catalog__count {
                font-size: 1.1rem;
            }
        }
    </style>
@endpush
