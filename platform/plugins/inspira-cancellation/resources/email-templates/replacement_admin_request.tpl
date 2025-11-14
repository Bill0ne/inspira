{{ header }}

<div class="bb-main-content">
    <table class="bb-box" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td class="bb-content">
                    <h1 class="bb-text-center">{{ 'plugins/inspira-cancellation::cancellation.email.templates.replacement_admin_request.title' | trans }}</h1>

                    <p class="bb-text-center bb-mb-md">{{ 'plugins/inspira-cancellation::cancellation.messages.replacement_admin_body' | trans({
                        'name': new_customer_name | default(''),
                        'email': new_customer_email | default(''),
                    }) }}</p>

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

                    <div class="bb-box bb-mt-md">
                        <p class="bb-m-0">{{ 'plugins/inspira-cancellation::cancellation.messages.replacement_requested_by' | trans({
                            'name': requested_by | default('plugins/inspira-cancellation::cancellation.messages.team_name' | trans),
                            'email': requested_email | default('plugins/inspira-cancellation::cancellation.messages.not_provided' | trans),
                        }) }}</p>
                    </div>

                    <p class="bb-mt-md">{{ 'plugins/inspira-cancellation::cancellation.messages.replacement_admin_hint' | trans }}</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{ footer }}
