@php
    $manageLicense = auth()
        ->user()
        ->hasPermission('core.manage.license');
@endphp

@if ($manageLicense)
    @include('core/base::system.partials.license-activation-modal')
@endif
