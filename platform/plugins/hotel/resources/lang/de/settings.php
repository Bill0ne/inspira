<?php

return [
    'hotel' => 'Hotel',
    'invoicing' => [
        'company_name' => 'Firmenname',
        'company_address' => 'Firmenadresse',
        'company_email' => 'Firmen-E-Mail',
        'company_phone' => 'Firmentelefon',
        'company_logo' => 'Firmenlogo',
    ],
    'using_custom_font_for_invoice' => 'Eigene Schriftart für Rechnung verwenden?',
    'invoice_font_family' => 'Schriftfamilie der Rechnung (funktioniert nur für lateinische Sprachen)',
    'enable_invoice_stamp' => 'Rechnungsstempel aktivieren?',
    'invoice_support_arabic_language' => 'Arabische Sprache in Rechnungen unterstützen?',
    'invoice_code_prefix' => 'Präfix für Rechnungscode',
    'invoice_settings' => 'Rechnung',
    'invoice_settings_description' => 'Einstellungen für Rechnungsinformationen',
    'general' => [
        'title' => 'Allgemein',
        'description' => 'Allgemeine Einstellungen für das Hotel',
        'enable_booking' => 'Buchungen aktivieren?',
        'maximum_number_of_guests' => 'Maximale Anzahl an Gästen',
        'minimum_number_of_guests' => 'Minimale Anzahl an Gästen',
        'booking_number_format' => [
            'title' => 'Format der Buchungsnummer (optional)',
            'description' => 'Die Standardbuchungsnummer beginnt bei einer bestimmten Zahl. Sie können Start- und Endnummer für die Buchungsnummer anpassen. Beispiel: Die Buchungsnummer wird als #:format angezeigt.',
            'start_with' => 'Startnummer',
            'end_with' => 'Endnummer',
        ],
        'booking_date_format' => 'Datumsformat für Buchungen',
        'default_system_date_format' => 'Standard-Datumsformat des Systems',
        'enable_food_order' => 'Essensbestellung aktivieren?',
    ],
    'review' => [
        'title' => 'Bewertungen',
        'description' => 'Bewertungseinstellungen für das Hotel',
        'enable_review_room' => 'Bewertungen aktivieren?',
        'reviews_per_page' => 'Anzahl der Bewertungen pro Seite?',
    ],
    'currency' => [
        'title' => 'Währungen',
        'description' => 'Liste der auf der Website verwendeten Währungen',
    ],
    'invoice' => [
        'title' => 'Rechnungen',
        'description' => 'Einstellungen für Rechnungsinformationen',
        'add_language_support' => 'Sprachunterstützung hinzufügen',
        'only_latin_languages' => 'Nur lateinische Sprachen',
        'confirm_reset' => 'Zurücksetzen der Rechnungsvorlage bestätigen?',
        'confirm_message' => 'Möchten Sie diese Rechnungsvorlage wirklich auf den Standard zurücksetzen?',
        'continue' => 'Fortfahren',
    ],
    'invoice_template' => [
        'title' => 'Rechnungsvorlage',
        'description' => 'Einstellungen für die Rechnungsvorlage',
    ],
];
