@php(Theme::set('pageTitle', 'Kurse'))
{{-- Inspira: Kursübersichtsseite – ohne Sidebar, volle Breite (12 Columns) --}}
{{-- Dateipfad: /platform/themes/inspira/views/courses.blade.php --}}

<section class="container courses-page my-5">
    {{-- Optional: Filterbereich (kommt später) --}}
    {{-- @include(Theme::getThemeNamespace('partials.filters')) --}}

    <div class="row">
        <div class="col-lg-12">
            {{-- Kursübersicht --}}
            {!! do_shortcode('[all-courses]') !!}
        </div>
    </div>
</section>
