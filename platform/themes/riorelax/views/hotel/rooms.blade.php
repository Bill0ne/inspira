@php(Theme::set('pageTitle', __('Rooms')))

<section class="container rooms-page mt-4 mb-5">
    {!! Theme::partial('filters', ['filterType' => 'rooms', 'filter_type' => 'rooms'], false) !!}
    <div class="row">
        <div class="col-lg-12">
            {!! do_shortcode('[all-rooms]') !!}
        </div>
    </div>
</section>
