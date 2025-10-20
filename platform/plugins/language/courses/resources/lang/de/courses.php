<?php

return [
    'manage' => 'Kurse verwalten',

    'course' => [
        'name'   => 'Kurse',
        'create' => 'Neuer Kurs',
        'price'  => 'Preis',
        'duration' => 'Dauer',
        'start_date' => 'Startdatum',
        'end_date' => 'Enddatum',
        'booking' => 'Kursbuchung',
        'booking_information' => 'Informationen zur Kursbuchung',
        'review' => 'Bewertungen',
        'unlimited_seats' => 'Unbegrenzte Plätze?',
        'number_of_seats' => 'Anzahl der Plätze',
        'is_recurring' => 'Wiederkehrender Kurs?',
        'recurring_type' => 'Wiederholungstyp',
        'recurring_interval' => 'Wiederholungsintervall',
        'recurring_until' => 'Wiederholung bis',
    ],

    'instructor' => [
        'name'   => 'Dozenten',
        'create' => 'Neuer Dozent',
        'bio'    => 'Biografie',
        'phone'  => 'Telefon',
        'instructor' => 'Dozent',
    ],

    'course-category' => [
        'name'   => 'Kategorien',
        'create' => 'Neue Kategorie',
        'category' => 'Kategorie',
    ],

    'course-session' => [
        'name'   => 'Kurstermine',
        'seats' => 'Sitzplatzdetails',
        'start_date' => 'Startdatum',
        'end_date' => 'Enddatum',
        'available_seats' => 'Verfügbare Plätze',
    ],

    'settings' => [
        'email' => [
            'title' => 'Kursbuchung',
            'description' => 'E-Mail-Konfiguration für Kursbuchungen',
            'templates' => [
                'notice_title' => 'Benachrichtigung an Administrator senden',
                'notice_description' => 'E-Mail-Vorlage zum Senden einer Benachrichtigung an den Administrator, wenn eine neue Kursbuchung eingeht',
                'booking_success_title' => 'Kursbestätigung an Gast senden',
                'booking_success_description' => 'E-Mail-Vorlage, um dem Gast eine Bestätigung der Kursbuchung zu senden',
                'booking_status_changed_title' => 'E-Mail senden, wenn sich der Buchungsstatus ändert',
                'booking_status_changed_description' => 'E-Mail-Vorlage zum Senden einer E-Mail an den Kunden, wenn sich der Kursbuchungsstatus ändert',
            ],
        ],
    ],
];
