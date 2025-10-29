@php(Theme::set('pageTitle', 'Kurse'))

@php
    Theme::asset()
        ->container('footer')
        ->usePath()
        ->add('courses-toolbar', 'js/courses-toolbar.js', ['jquery']);
@endphp

<section class="courses-page">
    {!! do_shortcode('[all-courses]') !!}
</section>
