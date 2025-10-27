<?php

return [
    'title' => 'Personal dashboard settings',
    'menu_title' => 'Personal dashboard',
    'menu_settings' => 'Dashboard settings',
    'general' => [
        'heading' => 'Global defaults',
        'allow_user_overrides' => 'Allow administrators to override global widget defaults on their personal dashboard',
        'save_button' => 'Save general settings',
    ],
    'widgets' => [
        'heading' => 'Widget order & availability',
        'description' => 'Define the default order and availability for each widget. Disabled widgets are hidden for everyone.',
        'table' => [
            'name' => 'Display name',
            'key' => 'Widget key',
            'order' => 'Order',
            'enabled' => 'Enabled',
        ],
        'save_button' => 'Save widget configuration',
    ],
    'custom_widgets' => [
        'heading' => 'Custom widgets',
        'description' => 'Create curated widgets for all administrators. Custom widgets can render Blade views or data supplied by handlers.',
        'create_button' => 'Add widget',
        'create_title' => 'Create custom widget',
        'edit_title' => 'Edit custom widget',
        'empty' => 'No custom widgets have been created yet.',
        'table' => [
            'name' => 'Widget',
            'key' => 'Key',
            'actions' => 'Actions',
        ],
        'fields' => [
            'key' => 'Widget key',
            'icon' => 'Icon',
            'color' => 'Color',
            'column_class' => 'Column layout classes',
            'view_path' => 'Blade view path',
            'handler' => 'Data handler class',
            'ajax_route' => 'Custom AJAX route',
            'sort_order' => 'Default order',
            'is_active' => 'Widget active',
            'has_load_callback' => 'Enable AJAX loading callback',
            'title' => 'Title',
            'description' => 'Description',
            'settings' => 'Widget settings (JSON)',
            'settings_placeholder' => 'Paste additional JSON settings here to pass into the widget instance.',
            'settings_hint' => 'You can provide additional widget settings in JSON format. Leave empty to keep the defaults.',
        ],
        'invalid_settings_json' => 'The widget settings must be valid JSON.',
    ],
    'alerts' => [
        'migrations_pending' => 'The personal dashboard database tables are missing. Run <code>:command</code> in your terminal, then reload this page.',
        'custom_widgets_pending' => 'Custom widget management will become available once the personal dashboard migrations have been executed.',
    ],
];
