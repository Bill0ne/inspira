<?php

return [
    'name' => 'plugins/inspira-cancellation::cancellation.email.title',
    'description' => 'plugins/inspira-cancellation::cancellation.email.description',
    'templates' => [
        'storno_customer' => [
            'title' => 'plugins/inspira-cancellation::cancellation.email.templates.storno_customer.title',
            'description' => 'plugins/inspira-cancellation::cancellation.email.templates.storno_customer.description',
            'subject' => 'Ihre Stornierung wurde erfasst',
            'can_off' => true,
        ],
        'storno_admin' => [
            'title' => 'plugins/inspira-cancellation::cancellation.email.templates.storno_admin.title',
            'description' => 'plugins/inspira-cancellation::cancellation.email.templates.storno_admin.description',
            'subject' => 'Neue Stornierung im Inspira Portal',
            'can_off' => true,
        ],
        'replacement_old_customer' => [
            'title' => 'plugins/inspira-cancellation::cancellation.email.templates.replacement_old_customer.title',
            'description' => 'plugins/inspira-cancellation::cancellation.email.templates.replacement_old_customer.description',
            'subject' => 'Ihre Buchung wurde übertragen',
            'can_off' => true,
        ],
        'replacement_new_customer' => [
            'title' => 'plugins/inspira-cancellation::cancellation.email.templates.replacement_new_customer.title',
            'description' => 'plugins/inspira-cancellation::cancellation.email.templates.replacement_new_customer.description',
            'subject' => 'Sie wurden als Ersatzteilnehmer:in eingetragen',
            'can_off' => true,
        ],
    ],
    'variables' => [
        'booking_reference' => 'plugins/inspira-cancellation::cancellation.email.variables.booking_reference',
        'booking_type' => 'plugins/inspira-cancellation::cancellation.email.variables.booking_type',
        'refund_amount' => 'plugins/inspira-cancellation::cancellation.email.variables.refund_amount',
        'refund_percent' => 'plugins/inspira-cancellation::cancellation.email.variables.refund_percent',
        'rule_description' => 'plugins/inspira-cancellation::cancellation.email.variables.rule_description',
        'days_until_start' => 'plugins/inspira-cancellation::cancellation.email.variables.days_until_start',
        'new_customer_name' => 'plugins/inspira-cancellation::cancellation.email.variables.new_customer_name',
        'new_customer_email' => 'plugins/inspira-cancellation::cancellation.email.variables.new_customer_email',
    ],
];
