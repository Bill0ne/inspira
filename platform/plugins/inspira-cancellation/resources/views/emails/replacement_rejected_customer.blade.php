@component('mail::message')
# {{ trans('plugins/inspira-cancellation::cancellation.email.templates.replacement_rejected_customer.title') }}

{{ trans('plugins/inspira-cancellation::cancellation.email.variables.booking_reference') }}: {{ $booking_reference ?? '' }}
{{ trans('plugins/inspira-cancellation::cancellation.email.variables.booking_type') }}: {{ $booking_type ?? '' }}

{{ trans('plugins/inspira-cancellation::cancellation.messages.replacement_rejected_body') }}

{{ trans('plugins/inspira-cancellation::cancellation.messages.replacement_support_hint') }}
@endcomponent
