<section class="courses-area pt-90 pb-90">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12">
                <div class="section-title center-align mb-50 text-center">
                    @if ($subtitle = $shortcode->subtitle)
                        <h5>{!! BaseHelper::clean($subtitle) !!}</h5>
                    @endif

                    @if ($title = $shortcode->title)
                        <h2>{!! BaseHelper::clean($title) !!}</h2>
                    @endif

                    @if ($description = $shortcode->description)
                        <p>{!! BaseHelper::clean($description) !!}</p>
                    @endif

                    <div class="mt-3 d-flex justify-content-center">
                        <a
                            href="https://inspira-zentrum.de/de/kurse"
                            class="btn btn-sm btn-outline-primary rounded-0 px-3 d-inline-flex align-items-center gap-2 text-primary"
                        >
                            Mehr Kurse
                            <span class="text-secondary">→</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @foreach($courses as $course)
                <div class="col-xl-4 col-md-6">
                    {!! Theme::partial('courses.item', compact('course')) !!}
                </div>
            @endforeach
        </div>
    </div>
</section>
