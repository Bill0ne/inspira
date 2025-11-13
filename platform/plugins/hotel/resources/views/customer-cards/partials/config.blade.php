<script>
    window.customerCard = {
        currency: "{{ get_application_currency()->symbol }}",
        routes: {
            apply: "{{ route('public.customer-card.apply') }}",
            remove: "{{ route('public.customer-card.remove') }}",
        }
    };
</script>
