{{ header }}

<div class="bb-main-content">
    <table class="bb-box" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td class="bb-content">
                    <h1 class="bb-text-center">{{ 'plugins/inspira-cancellation::cancellation.email.templates.storno_admin.title' | trans }}</h1>

                    <p class="bb-text-center bb-mb-md">{{ 'plugins/inspira-cancellation::cancellation.email.templates.storno_admin.description' | trans }}</p>

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
                                <td>{{ 'plugins/inspira-cancellation::cancellation.email.variables.total_amount' | trans }}</td>
                                <td class="bb-text-right">{{ total_amount }}</td>
                            </tr>
                            <tr>
                                <td>{{ 'plugins/inspira-cancellation::cancellation.frontend.refund_amount' | trans }}</td>
                                <td class="bb-text-right">{{ refund_amount }}</td>
                            </tr>
                            <tr>
                                <td>{{ 'plugins/inspira-cancellation::cancellation.frontend.refund_percent' | trans({'percent': refund_percent | default(0)}) }}</td>
                                <td class="bb-text-right">{{ refund_percent }}%</td>
                            </tr>
                            <tr>
                                <td>{{ 'plugins/inspira-cancellation::cancellation.frontend.fee_amount' | trans }}</td>
                                <td class="bb-text-right">{{ fee_amount }}</td>
                            </tr>
                            <tr>
                                <td>{{ 'plugins/inspira-cancellation::cancellation.email.variables.rule_description' | trans }}</td>
                                <td class="bb-text-right">{{ rule_description }}</td>
                            </tr>
                            {% if days_until_start is not null %}
                                <tr>
                                    <td>{{ 'plugins/inspira-cancellation::cancellation.email.variables.days_until_start' | trans }}</td>
                                    <td class="bb-text-right">{{ days_until_start }}</td>
                                </tr>
                            {% endif %}
                            {% if customer_name %}
                                <tr>
                                    <td>{{ 'plugins/inspira-cancellation::cancellation.email.variables.customer_name' | trans }}</td>
                                    <td class="bb-text-right">{{ customer_name }}</td>
                                </tr>
                            {% endif %}
                            {% if customer_email %}
                                <tr>
                                    <td>{{ 'plugins/inspira-cancellation::cancellation.email.variables.customer_email' | trans }}</td>
                                    <td class="bb-text-right">{{ customer_email }}</td>
                                </tr>
                            {% endif %}
                        </tbody>
                    </table>

                    {% if notes %}
                        <p class="bb-mt-md">
                            <strong>{{ 'plugins/inspira-cancellation::cancellation.email.variables.notes' | trans }}:</strong><br>
                            {{ notes }}
                        </p>
                    {% endif %}
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{ footer }}
