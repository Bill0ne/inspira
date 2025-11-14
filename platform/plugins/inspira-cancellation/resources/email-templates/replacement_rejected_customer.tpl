{{ header }}

<div class="bb-main-content">
    <table class="bb-box" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td class="bb-content">
                    <h1 class="bb-text-center">{{ 'plugins/inspira-cancellation::cancellation.email.templates.replacement_rejected_customer.title' | trans }}</h1>

                    <p><strong>{{ 'plugins/inspira-cancellation::cancellation.email.variables.booking_reference' | trans }}:</strong> {{ booking_reference }}</p>
                    <p><strong>{{ 'plugins/inspira-cancellation::cancellation.email.variables.booking_type' | trans }}:</strong> {{ booking_type }}</p>

                    <p class="bb-mt-md">{{ 'plugins/inspira-cancellation::cancellation.messages.replacement_rejected_body' | trans }}</p>

                    <p>{{ 'plugins/inspira-cancellation::cancellation.messages.replacement_support_hint' | trans }}</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{ footer }}
