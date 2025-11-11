{{ header }}

<div class="bb-main-content">
    <table class="bb-box" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td class="bb-content">
                    <h1 class="bb-text-center">{{ 'plugins/inspira-cancellation::cancellation.email.templates.storno_customer.title' | trans }}</h1>

                    <p class="bb-text-center bb-mb-md">
                        {{ 'plugins/inspira-cancellation::cancellation.frontend.cancel' | trans }}
                        {% if booking_type %} – {{ booking_type }}{% endif %}
                    </p>

                    <h3>{{ 'plugins/inspira-cancellation::cancellation.frontend.summary' | trans }}</h3>
                    <table class="bb-table" cellspacing="0" cellpadding="0">
                        <tbody>
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
                                <td>{{ 'plugins/inspira-cancellation::cancellation.email.variables.fee_amount' | trans }}</td>
                                <td class="bb-text-right">{{ fee_amount }}</td>
                            </tr>
                            <tr>
                                <td>{{ 'plugins/inspira-cancellation::cancellation.frontend.policy_info' | trans }}</td>
                                <td class="bb-text-right">{{ rule_description }}</td>
                            </tr>
                            {% if days_until_start is not null %}
                                <tr>
                                    <td>{{ 'plugins/inspira-cancellation::cancellation.frontend.days_until_start' | trans({'days': days_until_start}) }}</td>
                                    <td class="bb-text-right">{{ days_until_start }}</td>
                                </tr>
                            {% endif %}
                        </tbody>
                    </table>

                    <p class="bb-mt-md">
                        <strong>{{ 'plugins/inspira-cancellation::cancellation.email.variables.booking_reference' | trans }}:</strong>
                        {{ booking_reference }}
                    </p>

                    <p>{{ 'plugins/inspira-cancellation::cancellation.messages.cancellation_success' | trans }}</p>

                    <p>{{ site_title }}</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{ footer }}
