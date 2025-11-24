<section class="courses-area pt-20 pb-40">
    @if ($courses->isNotEmpty())
        <div class="row g-4 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
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
