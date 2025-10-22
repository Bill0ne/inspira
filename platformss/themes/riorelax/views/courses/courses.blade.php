@php(Theme::set('pageTitle', 'Kurse'))

<section class="container">
    <div class="row">
        <div class="col-lg-8">
            {!! do_shortcode('[all-courses]') !!}
        </div>
        <div class="col-lg-4">
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
    </div>
</section>
