<?php

return [
    'name' => 'plugins/courses::courses.settings.email.title',
    'description' => 'plugins/courses::courses.settings.email.description',
    'templates' => [
        'course-booking-confirmation' => [
            'title' => 'plugins/courses::courses.settings.email.templates.booking_success_title',
            'description' => 'plugins/courses::courses.settings.email.templates.booking_success_description',
            'subject' => 'Ihre Kursbuchungsbestätigung',
            'can_off' => true,
        ],
    ],
    'variables' => [
        'booking_type' => 'plugins/hotel::hotel.booking_type',
        'booking_name' => 'plugins/hotel::hotel.booking_name',
        'booking_email' => 'plugins/hotel::hotel.booking_email',
        'booking_phone' => 'plugins/hotel::hotel.booking_phone',
        'booking_address' => 'plugins/hotel::hotel.booking_address',
        'booking_request' => 'plugins/hotel::hotel.booking_request',
        'booking_link' => 'plugins/hotel::hotel.booking_link',
        'course_name' => 'Course Name',
        'session_name' => 'Session Name',
        'session_date' => 'Session Date',
    ],
];
