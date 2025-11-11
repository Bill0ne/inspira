@component('mail::message')
# {{ trans('plugins/inspira-cancellation::cancellation.email.templates.storno_admin.title') }}

{{ trans('plugins/inspira-cancellation::cancellation.email.variables.booking_reference') }}: {{ $booking_reference ?? '' }}
{{ trans('plugins/inspira-cancellation::cancellation.email.variables.booking_type') }}: {{ $booking_type ?? '' }}
{{ trans('plugins/inspira-cancellation::cancellation.frontend.refund_amount') }}: {{ $refund_amount ?? '' }}
{{ trans('plugins/inspira-cancellation::cancellation.frontend.refund_percent', ['percent' => $refund_percent ?? '0']) }}
{{ trans('plugins/inspira-cancellation::cancellation.frontend.policy_info') }}: {{ $rule_description ?? '' }}

@endcomponent
