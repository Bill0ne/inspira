@php(Theme::set('pageTitle', 'Kurse'))

<section class="container courses-page mt-4 mb-5">
    {!! Theme::partial('filters', ['filterType' => 'courses', 'filter_type' => 'courses'], false) !!}
    <div class="row">
        <div class="col-lg-12">
            {{-- Kursübersicht --}}
            {!! do_shortcode('[all-courses]') !!}
        </div>
    </div>
</section>
