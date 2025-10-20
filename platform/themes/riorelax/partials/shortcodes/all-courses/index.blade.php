<section class="courses-area pt-20 pb-40">
    <h3 class="mb-20">
        {{ __(':count Kurse verfügbar', ['count' => $courses->total()]) }}
    </h3>

    @if ($courses->isNotEmpty())
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-4 g-4 courses-grid">
            @foreach ($courses as $course)
                <div class="col">
                    {!! Theme::partial('courses.item', compact('course')) !!}
                </div>
            @endforeach
        </div>

        @if ($courses instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
            <div class="text-center mt-30">
                {!! $courses->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')) !!}
            </div>
        @endif
    @else
        <p>{{ __('Derzeit sind keine Kurse verfügbar') }}</p>
    @endif
</section>
