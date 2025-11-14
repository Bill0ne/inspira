@component('mail::message')
# {{ trans('plugins/inspira-cancellation::cancellation.menu') }}

{{ trans('plugins/inspira-cancellation::cancellation.frontend.cancel') }} – {{ $booking_type ?? '' }}

{{ trans('plugins/inspira-cancellation::cancellation.frontend.summary') }}:
- {{ trans('plugins/inspira-cancellation::cancellation.frontend.refund_amount') }}: {{ $refund_amount ?? '' }}
- {{ trans('plugins/inspira-cancellation::cancellation.frontend.refund_percent', ['percent' => $refund_percent ?? '0']) }}
- {{ trans('plugins/inspira-cancellation::cancellation.frontend.policy_info') }}: {{ $rule_description ?? '' }}

{{ trans('plugins/inspira-cancellation::cancellation.email.variables.booking_reference') }}: {{ $booking_reference ?? '' }}

{{ trans('plugins/inspira-cancellation::cancellation.messages.cancellation_pending_manual_review') }}

{{ trans('plugins/inspira-cancellation::cancellation.messages.cancellation_support_hint') }}

{{ config('app.name') }}
@endcomponent
