<?php

return [
    'name' => 'Kundenkarten',
    'created_message' => 'Kundenkarte wurde erfolgreich erstellt.',
    'save_button' => 'Karte speichern',
    'intro' => [
        'title' => 'Erstellen Sie Ihre erste Kundenkarte',
        'description' => 'Konfigurieren Sie wiederverwendbare Kartenkonten, die Kunden kaufen und im Checkout einsetzen können.',
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
        'is_active' => 'Aktiv',
    ],
    'form' => [
        'create' => 'Kundenkarte erstellen',
        'edit' => '„:name“ bearbeiten',
        'fields' => [
            'name' => 'Kartenname',
            'slug' => 'Slug',
            'type' => 'Kartentyp',
            'base_price' => 'Basispreis pro Einheit',
            'discount_percent' => 'Rabatt (%)',
            'units_total' => 'Gesamteinheiten',
            'units_remaining' => 'Verfügbare Einheiten',
            'valid_until' => 'Gültig bis',
            'assigned_to' => 'Kunde',
            'not_assigned' => 'Nicht zugewiesen',
            'is_active' => 'Aktiv',
        ],
        'summary' => [
            'title' => 'Gesamtpreis',
            'placeholder' => 'Preis, Einheiten und Rabatt ausfüllen, um den Gesamtpreis zu berechnen.',
            'discount_label' => 'Rabatt angewendet',
        ],
    ],
];
