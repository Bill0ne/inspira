<section class="courses-area pt-20 pb-40">
    @if ($courses->isNotEmpty())
        <div class="row g-4">
            @foreach ($courses as $course)
                <div class="col-lg-3 col-md-4 col-sm-6">
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
