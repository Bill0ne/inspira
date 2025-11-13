@php
    $config = $config ?? [];
    $scriptUrl = $config['scriptUrl'] ?? null;
    $shouldLoadScript = ! empty($config['active']) && ! empty($scriptUrl);
@endphp

<script>
    (function (win) {
        var payload = {!! \Illuminate\Support\Js::from($config) !!};
        win.checkoutPayment = win.checkoutPayment || {};
        win.checkoutPayment.providers = win.checkoutPayment.providers || {};
        win.checkoutPayment.providers.omise = win.checkoutPayment.providers.omise || {};
        win.checkoutPayment.providers.omise.config = payload;
    })(window);
</script>
@if ($shouldLoadScript)
    <script src="{{ $scriptUrl }}" defer></script>
@endif
