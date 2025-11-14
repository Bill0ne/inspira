{{ header }}

<div class="bb-main-content">
    <table class="bb-box" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td class="bb-content">
                    <h1 class="bb-text-center">{{ 'plugins/inspira-cancellation::cancellation.email.templates.storno_refund_paid.title' | trans }}</h1>

                    <p class="bb-text-center bb-mb-md">{{ 'plugins/inspira-cancellation::cancellation.messages.refund_paid_body' | trans({'amount': refund_amount | default('')}) }}</p>

                    <p><strong>{{ 'plugins/inspira-cancellation::cancellation.email.variables.booking_reference' | trans }}:</strong> {{ booking_reference }}</p>
                    <p><strong>{{ 'plugins/inspira-cancellation::cancellation.email.variables.booking_type' | trans }}:</strong> {{ booking_type }}</p>

                    <div class="bb-box bb-mt-md">
                        <p class="bb-m-0">{{ 'plugins/inspira-cancellation::cancellation.messages.refund_paid_summary' | trans({
                            'date': refund_paid_at | default(''),
                            'responsible': approver_name | default('plugins/inspira-cancellation::cancellation.messages.team_name' | trans),
                        }) }}</p>
                    </div>

                    <p class="bb-mt-md">{{ 'plugins/inspira-cancellation::cancellation.messages.cancellation_support_hint' | trans }}</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{ footer }}
