<?php

return [
    'name' => 'Kundenkarten',
    'created_message' => 'Kundenkarte wurde erfolgreich erstellt.',
    'assigned_message' => 'Kundenkarte wurde dem Kunden zugewiesen.',
    'save_button' => 'Karte speichern',
    'intro' => [
        'title' => 'Erstellen Sie Ihre erste Kundenkarte',
        'description' => 'Konfigurieren Sie digitale Kartenpakete, die Kunden kaufen und im Checkout einsetzen können.',
        'button_text' => 'Karte anlegen',
    ],
    'types' => [
        '5er' => '5er-Karte',
        '10er' => '10er-Karte',
        'custom' => 'Individuell',
    ],
    'table' => [
        'name' => 'Kartenname',
        'type' => 'Typ',
        'base_price' => 'Basispreis',
        'discount' => 'Rabatt %',
        'units_remaining' => 'Verbleibende Einheiten',
        'valid_until' => 'Gültig bis',
        'assigned_to' => 'Zugewiesen an',
        'status' => 'Status',
    ],
    'status' => [
        'active' => 'Aktiv',
        'warning' => 'Läuft bald ab',
        'expired' => 'Abgelaufen',
        'consumed' => 'Verbraucht',
    ],
    'form' => [
        'create' => 'Kundenkarte erstellen',
        'edit' => '„:name“ bearbeiten',
        'fields' => [
            'name' => 'Kartenname',
            'type' => 'Kartentyp',
            'base_price' => 'Basispreis pro Einheit',
            'discount_percent' => 'Rabatt (%)',
            'units_total' => 'Gesamteinheiten',
            'valid_until' => 'Gültig bis',
            'assigned_to' => 'Kunde',
            'not_assigned' => 'Nicht zugewiesen',
            'is_active' => 'Aktiv',
            'accept_customer_card' => 'Kundenkarten akzeptieren',
        ],
        'summary' => [
            'title' => 'Überblick',
            'placeholder' => 'Preis, Einheiten und Rabatt ausfüllen, um den Gesamtpreis zu berechnen.',
            'discount_label' => 'Rabatt angewendet',
        ],
    ],
    'messages' => [
        'select_card' => 'Bitte wählen Sie eine Kundenkarte aus.',
    ],
    'invoice' => [
        'discount_line' => 'Rabatt über Kundenkarte: :card',
    ],
];
