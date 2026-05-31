<section class="services-area pt-90 pb-90">
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
                </div>
            </div>
        </div>

        <div class="row services-active">
            @foreach ($members as $member)
                <div class="col-xl-4 col-md-6">
                    {!! Theme::partial('community.card', ['member' => $member]) !!}
                </div>
            @endforeach
        </div>

        <div class="services-controls" aria-label="{{ __('Community Navigation') }}"></div>
    </div>
</section>
