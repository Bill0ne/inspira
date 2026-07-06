<?php

return [
    'name' => 'plugins/hotel::booking.settings.email.title',
    'description' => 'plugins/hotel::booking.settings.email.description',
    'templates' => [
        'booking-notice-to-admin' => [
            'title' => 'plugins/hotel::booking.settings.email.templates.notice_title',
            'description' => 'plugins/hotel::booking.settings.email.templates.notice_description',
            'subject' => 'New Booking from {{ site_title }}',
            'can_off' => true,
        ],
        'booking-confirmation' => [
            'title' => 'plugins/hotel::booking.settings.email.templates.booking_success_title',
            'description' => 'plugins/hotel::booking.settings.email.templates.booking_success_description',
            'subject' => 'Booking Confirmation',
            'can_off' => true,
        ],
        'room-booking-confirmation' => [
            'title' => 'Raumbuchungsbestätigung',
            'description' => 'E-Mail an den Kunden nach einer Raumbuchung',
            'subject' => 'Ihre Raumbuchungsbestätigung',
            'can_off' => true,
        ],
        'booking-status-changed' => [
            'title' => 'plugins/hotel::booking.settings.email.templates.booking_status_changed_title',
            'description' => 'plugins/hotel::booking.settings.email.templates.booking_status_changed_description',
            'subject' => 'Your Booking Has Been Updated!',
            'can_off' => true,
        ],
        'booking-course-or-session-changed' => [
            'title' => 'Booking Course/Session Changed',
            'description' => 'Booking Course/Session Changed Description',
            'subject' => 'Your Course Booking Has Been Updated!',
            'can_off' => true,
        ],
        'customer-password-reset' => [
            'title' => 'Passwort zurücksetzen (Kunde)',
            'description' => 'E-Mail an den Kunden mit dem Link zum Zurücksetzen des Passworts',
            'subject' => 'Passwort zurücksetzen',
            'can_off' => false,
        ],
    ],
    'variables' => [
        'booking_type' => 'Booking Type',
        'booking_name' => 'plugins/hotel::hotel.booking_name',
        'booking_email' => 'plugins/hotel::hotel.booking_email',
        'booking_phone' => 'plugins/hotel::hotel.booking_phone',
        'booking_address' => 'plugins/hotel::hotel.booking_address',
        'booking_request' => 'plugins/hotel::hotel.booking_request',
        'booking_link' => 'plugins/hotel::hotel.booking_link',
        'booking_date' => 'plugins/hotel::hotel.booking_date',
        'booking_status' => 'plugins/hotel::hotel.booking_status',
        'course_name' => 'Course Name',
        'session_name' => 'Session Name',
        'reset_link' => 'Passwort-Reset-Link',
    ],
];
