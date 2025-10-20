@php(Theme::set('pageTitle', 'Kurse'))

<section class="container courses-page py-5">
    <div class="courses-filter-widget mb-5">
        <div class="sidebar-widget-rooms">
            <div class="sidebar-widget categories check-availability-custom">
                <div class="widget-content">
                    <div class="booking">
                        <div class="contact-bg">
                            {!! Theme::partial('courses.forms.form', ['style' => 1, 'availableForBooking' => false]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="courses-list">
        {!! do_shortcode('[all-courses]') !!}
    </div>
</section>
