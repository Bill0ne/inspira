<?php

return [
    'default_number_start_number' => env('COURSE_DEFAULT_NUMBER_START_NUMBER', 1000000),

    'performance' => [
        'max_views_reference' => env('COURSE_MAX_VIEWS_REFERENCE', 500),
        'analytics_period_days' => env('COURSE_ANALYTICS_PERIOD_DAYS', 30),
    ],
];
