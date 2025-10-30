@php(Theme::set('pageTitle', 'Kurse'))

<section class="container courses-page my-5">
    @include(Theme::getThemeNamespace('partials.filters'), ['filterType' => 'courses'])
    <div class="row">
        <div class="col-lg-12">
            {{-- Kursübersicht --}}
            {!! do_shortcode('[all-courses]') !!}
        </div>
    </div>
</section>
