{{ header }}

<div class="bb-main-content">
    <table class="bb-box" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td class="bb-content">
                    <h1 class="bb-text-center">{{ 'plugins/inspira-cancellation::cancellation.email.templates.storno_rejected.title' | trans }}</h1>

                    <p><strong>{{ 'plugins/inspira-cancellation::cancellation.email.variables.booking_reference' | trans }}:</strong> {{ booking_reference }}</p>
                    <p><strong>{{ 'plugins/inspira-cancellation::cancellation.email.variables.booking_type' | trans }}:</strong> {{ booking_type }}</p>

                    <p class="bb-mt-md">{{ 'plugins/inspira-cancellation::cancellation.messages.refund_rejected_body' | trans({'amount': refund_amount | default('')}) }}</p>

                    <p>{{ 'plugins/inspira-cancellation::cancellation.messages.cancellation_support_hint' | trans }}</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{ footer }}
