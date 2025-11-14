@component('mail::message')
# {{ trans('plugins/inspira-cancellation::cancellation.email.templates.storno_refund_paid.title') }}

{{ trans('plugins/inspira-cancellation::cancellation.email.variables.booking_reference') }}: {{ $booking_reference ?? '' }}
{{ trans('plugins/inspira-cancellation::cancellation.email.variables.booking_type') }}: {{ $booking_type ?? '' }}

{{ trans('plugins/inspira-cancellation::cancellation.messages.refund_paid_body', ['amount' => $refund_amount ?? '']) }}

@component('mail::panel')
{{ trans('plugins/inspira-cancellation::cancellation.messages.refund_paid_summary', [
    'date' => $refund_paid_at ?? '',
    'responsible' => $approver_name ?? trans('plugins/inspira-cancellation::cancellation.messages.team_name'),
]) }}
@endcomponent

{{ trans('plugins/inspira-cancellation::cancellation.messages.cancellation_support_hint') }}
@endcomponent
