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

        <div class="text-center mt-40">
            <a
                href="https://inspira-zentrum.de/de/kurse"
                class="d-inline-flex align-items-center gap-2 text-primary fw-semibold"
            >
                Mehr Kurse
                <i class="far fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
