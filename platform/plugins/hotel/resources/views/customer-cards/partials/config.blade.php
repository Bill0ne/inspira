@php
    $isCourseCheckout = request()->routeIs('public.course.*')
        || session()->has('course_checkout_token')
        || session('checkout_context') === \Botble\Hotel\Supports\HotelSupport::CONTEXT_COURSE;

    $routes = [
        'apply' => $isCourseCheckout ? route('ajax.customer-card.apply') : route('public.customer-card.apply'),
        'remove' => $isCourseCheckout ? route('ajax.customer-card.remove') : route('public.customer-card.remove'),
    ];
@endphp
<script>
    window.customerCard = window.customerCard || {};
    window.customerCard.currency = "{{ get_application_currency()->symbol }}";
    window.customerCard.routes = Object.assign({}, window.customerCard.routes || {}, {!! Js::from($routes) !!});
</script>
