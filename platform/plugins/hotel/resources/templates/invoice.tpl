<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ 'plugins/hotel::invoice.heading'|trans }} - #{{ invoice.code }}</title>
    {% if settings.using_custom_font_for_invoice %}
        <link href="https://fonts.googleapis.com/css2?family={{ settings.custom_font_family | urlencode }}:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    {% endif %}
    <style>
        @page {
            margin: 35px 35px 90px;
        }

        body {
            font-size: 14px;
            font-family: '{{ settings.font_family }}', Arial, sans-serif !important;
            color: #1f1f1f;
        }

        h1 {
            font-size: 26px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            padding: 6px 4px;
            vertical-align: top;
        }

        th {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .bold, strong, b, .total, .stamp {
            font-weight: 700;
        }

        .section-divider {
            border-bottom: 1px solid #dcdcdc;
            margin: 15px 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            gap: 40px;
            margin-bottom: 20px;
        }

        .header .logo img {
            max-height: 60px;
        }

        .company-info,
        .customer-info {
            line-height: 1.5;
            font-size: 13px;
        }

        .meta-table {
            margin-top: 10px;
            font-size: 13px;
        }

        .meta-table th {
            color: #555;
            font-weight: 600;
            width: 140px;
        }

        .items-table {
            width: 100%;
            margin-top: 25px;
            font-size: 13px;
        }

        .items-table thead th {
            border-bottom: 2px solid #222;
            text-transform: uppercase;
            font-size: 12px;
            padding: 10px 4px;
            color: #111;
        }

        .items-table tbody td {
            padding: 10px 4px;
            border-bottom: 1px solid #e6e6e6;
        }

        .items-table tbody tr:last-child td {
            border-bottom: none;
        }

        .items-table .description {
            width: 45%;
        }

        .summary {
            margin-top: 15px;
            width: 40%;
            margin-left: auto;
            font-size: 13px;
        }

        .summary tr th {
            text-align: left;
            font-weight: 600;
            padding-right: 10px;
        }

        .summary tr td {
            text-align: right;
        }

        .summary tr.total-row th,
        .summary tr.total-row td {
            font-size: 15px;
            border-top: 2px solid #222;
            padding-top: 10px;
        }

        .payment-terms {
            margin-top: 30px;
            font-size: 13px;
        }

        footer {
            position: fixed;
            bottom: 25px;
            left: 35px;
            right: 35px;
            font-size: 12px;
            color: #555;
        }

        .footer-divider {
            border-top: 1px solid #dcdcdc;
            margin-bottom: 8px;
            padding-top: 8px;
        }

        .footer-columns {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }

        .page-number {
            margin-top: 6px;
            text-align: right;
        }

        .page-number span::after {
            content: counter(page) " / " counter(pages);
        }

        .stamp {
            border: 2px solid #555;
            color: #555;
            display: inline-block;
            font-size: 18px;
            left: 30%;
            line-height: 1;
            opacity: .5;
            padding: .3rem .75rem;
            position: fixed;
            text-transform: uppercase;
            top: 40%;
            transform: rotate(-14deg);
        }

        .is-failed {
            border-color: #d23;
            color: #d23;
        }

        .is-completed {
            border-color: #0a9928;
            color: #0a9928;
        }
    </style>

    {{ invoice_header_filter | raw }}
</head>
<body>
{% if settings.enable_invoice_stamp %}
    {% if invoice.status == 'canceled' %}
        <span class="stamp is-failed">
            {{ invoice.status }}
        </span>
    {% elseif (invoice.payment.status) %}
        <span class="stamp {% if invoice.payment.status == 'completed' %} is-completed {% else %} is-failed {% endif %}">
            {{ invoice.payment.status }}
        </span>
    {% endif %}
{% endif %}

<div class="header">
    <div class="logo">
        <img src="{{ logo_full_path }}" alt="{{ settings.company_name_for_invoicing }}">
    </div>
    <div class="company-info">
        <strong>{{ settings.company_name_for_invoicing }}</strong><br>
        {% if settings.company_address_for_invoicing %}
            {{ settings.company_address_for_invoicing|nl2br|raw }}<br>
        {% endif %}
        {% if settings.company_email_for_invoicing %}
            {{ settings.company_email_for_invoicing }}<br>
        {% endif %}
        {% if settings.company_phone_for_invoicing %}
            {{ settings.company_phone_for_invoicing }}
        {% endif %}
    </div>
    <div class="customer-info">
        <strong>{{ invoice.customer_name }}</strong><br>
        {% if invoice.customer_address %}
            {{ invoice.customer_address|nl2br|raw }}<br>
        {% endif %}
        {% if invoice.customer_email %}
            {{ invoice.customer_email }}<br>
        {% endif %}
        {% if invoice.customer_phone %}
            {{ invoice.customer_phone }}
        {% endif %}
    </div>
</div>

<div class="section-divider"></div>

<div class="invoice-meta">
    <h1>{{ 'plugins/hotel::invoice.heading'|trans }} {{ invoice.code }}</h1>

    <table class="meta-table">
        <tr>
            <th>{{ 'plugins/hotel::invoice.meta.invoice_number'|trans }}</th>
            <td>{{ invoice.code }}</td>
        </tr>
        {% if invoice.customer_id %}
        <tr>
            <th>{{ 'plugins/hotel::invoice.meta.customer_number'|trans }}</th>
            <td>{{ invoice.customer_id }}</td>
        </tr>
        {% endif %}
        <tr>
            <th>{{ 'plugins/hotel::invoice.meta.invoice_date'|trans }}</th>
            <td>{{ invoice.created_at|date('d.m.Y') }}</td>
        </tr>
        <tr>
            <th>{{ 'plugins/hotel::invoice.meta.service_date'|trans }}</th>
            <td>{{ invoice.created_at|date('d.m.Y') }}</td>
        </tr>
    </table>
</div>

<table class="items-table">
    <thead>
        <tr>
            <th style="width: 50px;">{{ 'plugins/hotel::invoice.item.position'|trans }}</th>
            <th class="description">{{ 'plugins/hotel::invoice.item.description'|trans }}</th>
            <th class="text-right" style="width: 80px;">{{ 'plugins/hotel::invoice.item.qty'|trans }}</th>
            <th style="width: 80px;">{{ 'plugins/hotel::invoice.item.unit'|trans }}</th>
            <th class="text-right" style="width: 110px;">{{ 'plugins/hotel::invoice.item.unit_price'|trans }}</th>
            <th class="text-right" style="width: 120px;">{{ 'plugins/hotel::invoice.item.total_price'|trans }}</th>
        </tr>
    </thead>
    <tbody>
        {% for item in invoice.items %}
            <tr>
                <td>{{ loop.index }}</td>
                <td>
                    <strong>{{ item.name }}</strong>
                    {% if item.description %}<br><small>{{ item.description }}</small>{% endif %}
                </td>
                <td class="text-right">{{ item.qty }}</td>
                <td>{{ 'plugins/hotel::invoice.item.unit_value'|trans }}</td>
                <td class="text-right">{{ item.sub_total|price_format }}</td>
                <td class="text-right">{{ (item.sub_total * item.qty)|price_format }}</td>
            </tr>
        {% else %}
            <tr>
                <td colspan="6">{{ 'plugins/hotel::invoice.item.empty'|trans }}</td>
            </tr>
        {% endfor %}
    </tbody>
</table>

<table class="summary">
    <tr>
        <th>{{ 'plugins/hotel::invoice.sub_total_net'|trans }}</th>
        <td>{{ invoice.sub_total|price_format }}</td>
    </tr>
    {% if invoice.discount_amount %}
        <tr>
            <th>{{ 'plugins/hotel::invoice.discount_amount'|trans }}</th>
            <td>-{{ invoice.discount_amount|price_format }}</td>
        </tr>
    {% endif %}
    {% if invoice.tax_amount %}
        <tr>
            <th>{{ 'plugins/hotel::invoice.tax_label'|trans }}</th>
            <td>{{ invoice.tax_amount|price_format }}</td>
        </tr>
    {% endif %}
    {% if invoice.shipping_amount %}
        <tr>
            <th>{{ 'plugins/hotel::invoice.shipping_fee'|trans }}</th>
            <td>{{ invoice.shipping_amount|price_format }}</td>
        </tr>
    {% endif %}
    <tr class="total-row">
        <th>{{ 'plugins/hotel::invoice.total_amount'|trans }}</th>
        <td>{{ invoice.amount|price_format }}</td>
    </tr>
</table>

<div class="payment-terms">
    <p>{{ 'plugins/hotel::invoice.payment_terms'|trans }}</p>
    <p>{{ 'plugins/hotel::invoice.thank_you'|trans }}</p>
</div>

{{ hotel_invoice_footer | raw }}

<footer>
    <div class="footer-divider"></div>
    <div class="footer-columns">
        <div>
            <strong>{{ settings.company_name_for_invoicing }}</strong><br>
            {% if settings.company_address_for_invoicing %}
                {{ settings.company_address_for_invoicing|nl2br|raw }}
            {% endif %}
        </div>
        <div>
            {% if settings.company_email_for_invoicing %}
                {{ settings.company_email_for_invoicing }}<br>
            {% endif %}
            {% if settings.company_phone_for_invoicing %}
                {{ settings.company_phone_for_invoicing }}
            {% endif %}
        </div>
    </div>
    <div class="page-number">{{ 'plugins/hotel::invoice.page'|trans }} <span></span></div>
</footer>
</body>
</html>
