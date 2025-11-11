{{ header }}

<div class="bb-main-content">
    <table class="bb-box" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td class="bb-content">
                    <h1 class="bb-text-center">{{ 'plugins/inspira-cancellation::cancellation.email.templates.replacement_old_customer.title' | trans }}</h1>

                    <p class="bb-text-center bb-mb-md">{{ 'plugins/inspira-cancellation::cancellation.email.templates.replacement_old_customer.description' | trans }}</p>

                    <table class="bb-table" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr>
                                <td>{{ 'plugins/inspira-cancellation::cancellation.email.variables.booking_reference' | trans }}</td>
                                <td class="bb-text-right">{{ booking_reference }}</td>
                            </tr>
                            <tr>
                                <td>{{ 'plugins/inspira-cancellation::cancellation.email.variables.booking_type' | trans }}</td>
                                <td class="bb-text-right">{{ booking_type }}</td>
                            </tr>
                            <tr>
                                <td>{{ 'plugins/inspira-cancellation::cancellation.email.variables.new_customer_name' | trans }}</td>
                                <td class="bb-text-right">{{ new_customer_name }}</td>
                            </tr>
                            <tr>
                                <td>{{ 'plugins/inspira-cancellation::cancellation.email.variables.new_customer_email' | trans }}</td>
                                <td class="bb-text-right">{{ new_customer_email }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p class="bb-mt-md">{{ 'plugins/inspira-cancellation::cancellation.messages.replacement_success' | trans }}</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{ footer }}
