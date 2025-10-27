<?php

return [
    [
        'name' => 'Personal Dashboard',
        'flag' => 'personal-dashboard.settings',
    ],
    [
        'name' => 'Dashboard settings',
        'flag' => 'personal-dashboard.settings.index',
        'parent_flag' => 'personal-dashboard.settings',
    ],
    [
        'name' => 'Manage custom widgets',
        'flag' => 'personal-dashboard.settings.custom-widgets',
        'parent_flag' => 'personal-dashboard.settings',
    ],
];
