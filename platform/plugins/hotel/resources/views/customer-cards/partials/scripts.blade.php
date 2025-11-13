@php
    $shouldRegisterCustomerCardAssets = is_in_admin() || \Illuminate\Support\Facades\Route::is('public.course.*');
@endphp

@if (! $shouldRegisterCustomerCardAssets)
    {!! $jsValidator ?? '' !!}
@else
    @push('header')
        <script>
            'use strict';

            window.customerCard = window.customerCard || {};
            window.customerCard.currency = '{{ get_application_currency()->symbol }}';
            window.customerCard.routes = {
                apply: '{{ route('ajax.customer-card.apply') }}',
                remove: '{{ route('ajax.customer-card.remove') }}',
            };
            window.trans = window.trans || {};
            window.trans.customerCard = {{ Js::from(trans('plugins/hotel::customer-card')) }};
        </script>
    @endpush

    @push('footer')
        {!! $jsValidator ?? '' !!}

        @if (! is_in_admin())
            @php
                Theme::asset()
                    ->container('footer')
                    ->add('hotel-customer-card-js', 'vendor/core/plugins/hotel/js/customer-card.js', ['jquery']);
            @endphp
        @endif
    @endpush
@endif
