{{ header }}

<div class="bb-main-content">
    <table class="bb-box" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td class="bb-content">
                    <h1 class="bb-text-center">{{ 'plugins/inspira-cancellation::cancellation.email.templates.storno_refund_approved.title' | trans }}</h1>

                    <p class="bb-text-center bb-mb-md">{{ 'plugins/inspira-cancellation::cancellation.messages.refund_approved_body' | trans({'amount': refund_amount | default(''), 'percent': refund_percent | default('0')}) }}</p>

                    <p><strong>{{ 'plugins/inspira-cancellation::cancellation.email.variables.booking_reference' | trans }}:</strong> {{ booking_reference }}</p>
                    <p><strong>{{ 'plugins/inspira-cancellation::cancellation.email.variables.booking_type' | trans }}:</strong> {{ booking_type }}</p>

                    <p>{{ 'plugins/inspira-cancellation::cancellation.messages.refund_timeframe_hint' | trans }}</p>

                    <div class="bb-box bb-mt-md">
                        <p class="bb-m-0">{{ 'plugins/inspira-cancellation::cancellation.messages.refund_summary_line' | trans({
                            'status': refund_status | default('plugins/inspira-cancellation::cancellation.statuses.approved' | trans),
                            'approver': approver_name | default('plugins/inspira-cancellation::cancellation.messages.team_name' | trans),
                            'date': refund_approved_at | default(''),
                        }) }}</p>
                    </div>

                    <p class="bb-mt-md">{{ 'plugins/inspira-cancellation::cancellation.messages.cancellation_support_hint' | trans }}</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{ footer }}
