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
    'forms' => [
        'code' => 'Code',
        'label' => 'Label',
        'description' => 'Description',
        'price_tier' => 'Price Tier',
        'select' => 'Select',
        'customer_category' => 'Customer Category',
        'direction' => 'Direction',
        'scope' => 'Scope',
        'target_type' => 'Target Type',
        'applicable_targets' => 'Applicable Categories / Products',
        'calculation_type' => 'Calculation Type',
        'calculation_value' => 'Calculation Value',
        'rounding_mode' => 'Rounding Mode',
        'round_to' => 'Round To',
        'title' => 'Title',
        'condition_type' => 'Condition Type',
        'range_min' => 'Range Min',
        'range_max' => 'Range Max',
        'discount_type' => 'Discount Type',
        'discount_value' => 'Discount Value',
        'priority' => 'Priority',
        'tier_name' => 'Tier Name',
        'is_exclusive' => 'Is Exclusive',
        'starts_at' => 'Starts At',
        'ends_at' => 'Ends At',
        'notes' => 'Notes',
    ],
    'tables' => [
        'exclusive' => 'Exclusive',
    ],
    'options' => [
        'yes' => 'Yes',
        'no' => 'No',
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
        ],
    ],
];
