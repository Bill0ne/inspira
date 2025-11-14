@component('mail::message')
# {{ trans('plugins/inspira-cancellation::cancellation.email.templates.replacement_admin_request.title') }}

{{ trans('plugins/inspira-cancellation::cancellation.email.variables.booking_reference') }}: {{ $booking_reference ?? '' }}
{{ trans('plugins/inspira-cancellation::cancellation.email.variables.booking_type') }}: {{ $booking_type ?? '' }}

{{ trans('plugins/inspira-cancellation::cancellation.messages.replacement_admin_body', [
    'name' => $new_customer_name ?? '',
    'email' => $new_customer_email ?? '',
]) }}

@component('mail::panel')
- {{ trans('plugins/inspira-cancellation::cancellation.messages.replacement_requested_by', ['name' => $requested_by ?? trans('plugins/inspira-cancellation::cancellation.messages.team_name'), 'email' => $requested_email ?? trans('plugins/inspira-cancellation::cancellation.messages.not_provided')]) }}
@endcomponent

{{ trans('plugins/inspira-cancellation::cancellation.messages.replacement_admin_hint') }}
@endcomponent
