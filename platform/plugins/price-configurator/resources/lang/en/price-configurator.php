<?php

return [
    'name' => 'Price Configurator',
    'customer-category' => [
        'name' => 'Customer category',
        'create' => 'Create',
    ],
    'tier' => [
        'name' => 'Tier',
    ],
    'rule' => [
        'name' => 'Rule',
    ],
    'quantity-discount' => [
        'name' => 'Quantity discount',
    ],
    'enums' => [
        'statuses' => [
            'active' => 'Active',
            'inactive' => 'Inactive',
        ],
        'scope' => [
            'all_products' => 'All Products',
            'by_category' => 'By Category',
            'specific_products' => 'Specific Products',
        ],
        'calculation_type' => [
            'absolute' => 'Absolute',
            'percent' => 'Percent',
        ],
        'rounding_mode' => [
            'none' => 'None',
            'up' => 'Up',
            'down' => 'Down',
            'nearest' => 'Nearest',
        ],
        'condition_type' => [
            'amount' => 'Amount',
            'quantity' => 'Quantity',
        ],
        'target_type' => [
            'room' => 'Room',
            'course' => 'Course',
        ],
        'rule_direction' => [
            'increase' => 'Increase',
            'decrease' => 'Decrease',
        ]
    ]
];
