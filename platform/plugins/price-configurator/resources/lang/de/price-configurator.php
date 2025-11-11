<?php

return [
    'name' => 'Preiskonfigurator',
    'customer-category' => [
        'name' => 'Kundenkategorie',
        'create' => 'Erstellen',
    ],
    'tier' => [
        'name' => 'Preisstaffel',
    ],
    'rule' => [
        'name' => 'Regel',
    ],
    'quantity-discount' => [
        'name' => 'Mengenrabatt',
    ],
    'forms' => [
        'code' => 'Code',
        'label' => 'Bezeichnung',
        'description' => 'Beschreibung',
        'price_tier' => 'Preisstaffel',
        'select' => 'Auswählen',
        'customer_category' => 'Kundenkategorie',
        'direction' => 'Richtung',
        'scope' => 'Geltungsbereich',
        'target_type' => 'Zieltyp',
        'applicable_targets' => 'Anwendbare Kategorien / Produkte',
        'calculation_type' => 'Berechnungstyp',
        'calculation_value' => 'Berechnungswert',
        'rounding_mode' => 'Rundungsmodus',
        'round_to' => 'Runden auf',
        'title' => 'Titel',
        'condition_type' => 'Bedingungstyp',
        'range_min' => 'Unterer Bereich',
        'range_max' => 'Oberer Bereich',
        'discount_type' => 'Rabattart',
        'discount_value' => 'Rabattwert',
        'priority' => 'Priorität',
        'tier_name' => 'Name der Preisstaffel',
        'is_exclusive' => 'Exklusiv',
        'starts_at' => 'Beginnt am',
        'ends_at' => 'Endet am',
        'notes' => 'Notizen',
    ],
    'tables' => [
        'exclusive' => 'Exklusiv',
    ],
    'options' => [
        'yes' => 'Ja',
        'no' => 'Nein',
    ],
    'enums' => [
        'statuses' => [
            'active' => 'Aktiv',
            'inactive' => 'Inaktiv',
        ],
        'scope' => [
            'all_products' => 'Alle Produkte',
            'by_category' => 'Nach Kategorie',
            'specific_products' => 'Spezifische Produkte',
        ],
        'calculation_type' => [
            'absolute' => 'Absolut',
            'percent' => 'Prozentual',
        ],
        'rounding_mode' => [
            'none' => 'Keine',
            'up' => 'Aufrunden',
            'down' => 'Abrunden',
            'nearest' => 'Zum nächsten',
        ],
        'condition_type' => [
            'amount' => 'Betrag',
            'quantity' => 'Menge',
        ],
        'target_type' => [
            'room' => 'Zimmer',
            'course' => 'Kurs',
        ],
        'rule_direction' => [
            'increase' => 'Erhöhen',
            'decrease' => 'Verringern',
        ],
    ],
];
