<?php

return [
    'name' => 'Customer cards',
    'created_message' => 'Customer card created successfully.',
    'save_button' => 'Save card',
    'intro' => [
        'title' => 'Create your first customer card',
        'description' => 'Design reusable card bundles that customers can purchase and redeem during checkout.',
        'button_text' => 'Create card',
    ],
    'types' => [
        '5er' => '5 units',
        '10er' => '10 units',
        'custom' => 'Custom',
    ],
    'table' => [
        'name' => 'Card name',
        'type' => 'Type',
        'base_price' => 'Base price',
        'discount' => 'Discount %',
        'units_remaining' => 'Units remaining',
        'valid_until' => 'Valid until',
        'is_active' => 'Active',
    ],
    'form' => [
        'create' => 'Create customer card',
        'edit' => 'Edit ":name"',
        'fields' => [
            'name' => 'Card name',
            'slug' => 'Slug',
            'type' => 'Card type',
            'base_price' => 'Base price per unit',
            'discount_percent' => 'Discount (%)',
            'units_total' => 'Total units',
            'units_remaining' => 'Units remaining',
            'valid_until' => 'Valid until',
            'assigned_to' => 'Assign to customer',
            'not_assigned' => 'Not assigned',
            'is_active' => 'Active',
        ],
        'summary' => [
            'title' => 'Total price',
            'placeholder' => 'Fill in price, units and discount to calculate the total.',
            'discount_label' => 'discount applied',
        ],
    ],
];
