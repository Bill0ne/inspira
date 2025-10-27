<?php

return [
    'title' => 'Dashboard-Einstellungen',
    'menu_title' => 'Persönliches Dashboard',
    'menu_settings' => 'Dashboard Einstellungen',
    'general' => [
        'heading' => 'Globale Standardwerte',
        'allow_user_overrides' => 'Administratoren dürfen globale Widget-Standards auf ihrem Dashboard überschreiben',
        'save_button' => 'Allgemeine Einstellungen speichern',
    ],
    'widgets' => [
        'heading' => 'Widget-Reihenfolge & Verfügbarkeit',
        'description' => 'Lege Reihenfolge und Verfügbarkeit der Widgets fest. Deaktivierte Widgets werden für alle ausgeblendet.',
        'table' => [
            'name' => 'Anzeigename',
            'key' => 'Widget-Schlüssel',
            'order' => 'Reihenfolge',
            'enabled' => 'Aktiv',
        ],
        'save_button' => 'Widget-Konfiguration speichern',
    ],
    'custom_widgets' => [
        'heading' => 'Eigene Widgets',
        'description' => 'Erstelle kuratierte Widgets für alle Administratoren. Eigene Widgets können Blade-Views oder Daten aus Handlern rendern.',
        'create_button' => 'Widget hinzufügen',
        'create_title' => 'Eigenes Widget erstellen',
        'edit_title' => 'Eigenes Widget bearbeiten',
        'empty' => 'Es wurden noch keine eigenen Widgets angelegt.',
        'table' => [
            'name' => 'Widget',
            'key' => 'Schlüssel',
            'actions' => 'Aktionen',
        ],
        'fields' => [
            'key' => 'Widget-Schlüssel',
            'icon' => 'Icon',
            'color' => 'Farbe',
            'column_class' => 'Spalten-Klassen',
            'view_path' => 'Blade-View-Pfad',
            'handler' => 'Handler-Klasse',
            'ajax_route' => 'Eigene AJAX-Route',
            'sort_order' => 'Standard-Reihenfolge',
            'is_active' => 'Widget aktiv',
            'has_load_callback' => 'AJAX-Nachladen aktivieren',
            'title' => 'Titel',
            'description' => 'Beschreibung',
            'settings' => 'Widget-Einstellungen (JSON)',
            'settings_placeholder' => 'Füge hier zusätzliche JSON-Einstellungen für das Widget ein.',
            'settings_hint' => 'Gib optionale JSON-Einstellungen an oder lasse das Feld leer, um die Standardwerte zu verwenden.',
        ],
        'invalid_settings_json' => 'Die Widget-Einstellungen müssen gültiges JSON sein.',
    ],
];
