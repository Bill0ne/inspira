@component('mail::message')
# {{ trans('plugins/inspira-cancellation::cancellation.email.templates.replacement_old_customer.title') }}

{{ trans('plugins/inspira-cancellation::cancellation.email.variables.booking_reference') }}: {{ $booking_reference ?? '' }}
{{ trans('plugins/inspira-cancellation::cancellation.email.variables.booking_type') }}: {{ $booking_type ?? '' }}

{{ trans('plugins/inspira-cancellation::cancellation.frontend.replacement_intro') }}
@endcomponent
