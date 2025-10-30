@php(Theme::set('pageTitle', __('Rooms')))

<section class="container rooms-page mt-4 mb-5">
    @include(Theme::getThemeNamespace('partials.filters'), ['filterType' => 'rooms'])
    <div class="row">
        <div class="col-lg-12">
            {!! do_shortcode('[all-rooms]') !!}
        </div>
    </div>
</section>
