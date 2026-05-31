<section class="services-area pt-90 pb-90">
    <div class="container">
        @if (! empty($shortcode->subtitle) || ! empty($shortcode->title))
            <div class="row justify-content-center">
                <div class="col-xl-12">
                    <div class="section-title center-align mb-50 text-center">
                        @if ($subtitle = $shortcode->subtitle)
                            <h5>{!! BaseHelper::clean($subtitle) !!}</h5>
                        @endif

                        @if ($title = $shortcode->title)
                            <h2>{!! BaseHelper::clean($title) !!}</h2>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div class="row g-4">
            @foreach ($members as $member)
                <div class="col-xl-3 col-lg-3 col-md-6 col-12">
                    {!! Theme::partial('community.card', ['member' => $member]) !!}
                </div>
            @endforeach
        </div>
    </div>
</section>
