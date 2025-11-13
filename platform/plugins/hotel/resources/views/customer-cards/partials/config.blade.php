<script>
    window.customerCard = window.customerCard || {};
    window.customerCard.currency = "{{ get_application_currency()->symbol }}";
    window.customerCard.routes = {
        apply: "{{ route('public.customer-card.apply') }}",
        remove: "{{ route('public.customer-card.remove') }}",
    };
</script>
