@component('mail::message')
# {{ trans('plugins/inspira-cancellation::cancellation.email.templates.storno_refund_approved.title') }}

{{ trans('plugins/inspira-cancellation::cancellation.email.variables.booking_reference') }}: {{ $booking_reference ?? '' }}
{{ trans('plugins/inspira-cancellation::cancellation.email.variables.booking_type') }}: {{ $booking_type ?? '' }}

{{ trans('plugins/inspira-cancellation::cancellation.messages.refund_approved_body', [
    'amount' => $refund_amount ?? '',
    'percent' => $refund_percent ?? '0',
]) }}

{{ trans('plugins/inspira-cancellation::cancellation.messages.refund_timeframe_hint') }}

@component('mail::panel')
{{ trans('plugins/inspira-cancellation::cancellation.messages.refund_summary_line', [
    'status' => $refund_status ?? trans('plugins/inspira-cancellation::cancellation.statuses.approved'),
    'approver' => $approver_name ?? trans('plugins/inspira-cancellation::cancellation.messages.team_name'),
    'date' => $refund_approved_at ?? '',
]) }}
@endcomponent

{{ trans('plugins/inspira-cancellation::cancellation.messages.cancellation_support_hint') }}
@endcomponent
