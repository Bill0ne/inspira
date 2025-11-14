@component('mail::message')
# {{ trans('plugins/inspira-cancellation::cancellation.email.templates.storno_rejected.title') }}

{{ trans('plugins/inspira-cancellation::cancellation.email.variables.booking_reference') }}: {{ $booking_reference ?? '' }}
{{ trans('plugins/inspira-cancellation::cancellation.email.variables.booking_type') }}: {{ $booking_type ?? '' }}

{{ trans('plugins/inspira-cancellation::cancellation.messages.refund_rejected_body', ['amount' => $refund_amount ?? '']) }}

{{ trans('plugins/inspira-cancellation::cancellation.messages.cancellation_support_hint') }}
@endcomponent
