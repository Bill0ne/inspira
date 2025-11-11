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
@endpush
