@component('mail::message')
# {{ trans('plugins/inspira-cancellation::cancellation.email.templates.replacement_new_customer.title') }}

{{ trans('plugins/inspira-cancellation::cancellation.email.variables.booking_reference') }}: {{ $booking_reference ?? '' }}
{{ trans('plugins/inspira-cancellation::cancellation.email.variables.booking_type') }}: {{ $booking_type ?? '' }}

{{ trans('plugins/inspira-cancellation::cancellation.messages.replacement_welcome', ['name' => $new_customer_name ?? '']) }}

{{ trans('plugins/inspira-cancellation::cancellation.messages.replacement_support_hint') }}
@endcomponent
