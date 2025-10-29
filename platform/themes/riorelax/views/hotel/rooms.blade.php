@php(Theme::set('pageTitle', __('Rooms')))

<section class="container rooms-page my-5">
    {{-- Optional: zukünftiger Filterbereich --}}
    {{-- @include(Theme::getThemeNamespace('partials.room-filters')) --}}

    <div class="row">
        <div class="col-lg-12">
            {!! do_shortcode('[all-rooms]') !!}
        </div>
    </div>
</section>
