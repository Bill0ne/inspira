@php(Theme::set('pageTitle', 'Kurse'))

<section class="courses-page container py-60">
    <div class="courses-page__hero text-center">
        <h1 class="courses-page__title">{{ __('Kurse') }}</h1>
        <p class="courses-page__subtitle">{{ __('Finde den passenden Kurs und buche deinen Platz in wenigen Klicks.') }}</p>
    </div>

    <div class="courses-page__filters">
        {!! Theme::partial('courses.forms.form', ['style' => 'toolbar', 'availableForBooking' => false]) !!}
    </div>

    <div class="courses-page__results">
        {!! do_shortcode('[all-courses]') !!}
    </div>
</section>

@push('styles')
    <style>
        .courses-page {padding-top: 4rem;padding-bottom: 6rem;}
        .courses-page__hero {margin-bottom: 2.5rem;}
        .courses-page__title {font-size: clamp(2rem, 3vw, 2.8rem);font-weight: 700;margin-bottom: .5rem;}
        .courses-page__subtitle {color: #4f4f4f;font-size: 1rem;margin: 0 auto;max-width: 38rem;}

        .courses-page__filters {margin-bottom: 2.75rem;}

        @media (max-width: 575.98px) {
            .courses-page {padding-top: 3rem;padding-bottom: 4rem;}
            .courses-page__title {font-size: 1.8rem;}
            .courses-page__subtitle {font-size: .95rem;}
        }
    </style>
@endpush
