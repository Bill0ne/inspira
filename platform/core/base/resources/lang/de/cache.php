<?php

return [
    'cache_management' => 'Cache-Verwaltung',
    'cache_management_description' => 'Leere den Cache, um deine Website auf dem neuesten Stand zu halten.',
    'cache_commands' => 'Cache-Bereinigungsbefehle',
    'current_size' => 'Aktuelle Größe',
    'clear_button' => 'Leeren',
    'refresh_button' => 'Aktualisieren',
    'cache_size_warning' => 'Der CMS-Cache ist ziemlich groß (>50 MB). Ein Leeren kann die Systemleistung verbessern.',
    'footer_note' => 'Leere den Cache, nachdem du Änderungen an der Website vorgenommen hast, damit sie korrekt angezeigt werden.',
    'commands' => [
        'clear_cms_cache' => [
            'title' => 'Gesamten CMS-Cache leeren',
            'description' => 'Leert den CMS-Cache: Datenbank-Cache, statische Blöcke usw. Führe diesen Befehl aus, wenn Änderungen nach einer Aktualisierung nicht sichtbar sind.',
            'success_msg' => 'Cache geleert',
        ],
        'refresh_compiled_views' => [
            'title' => 'Kompilierte Views aktualisieren',
            'description' => 'Leert kompilierte Views, damit Vorlagen aktuell sind.',
            'success_msg' => 'View-Cache aktualisiert',
        ],
        'clear_config_cache' => [
            'title' => 'Konfigurations-Cache leeren',
            'description' => 'Aktualisiere den Konfigurations-Cache, wenn du etwas in der Produktionsumgebung änderst.',
            'success_msg' => 'Konfigurations-Cache geleert',
        ],
        'clear_route_cache' => [
            'title' => 'Routen-Cache leeren',
            'description' => 'Leert den Routing-Cache.',
            'success_msg' => 'Der Routen-Cache wurde geleert',
        ],
        'clear_log' => [
            'title' => 'Protokolle löschen',
            'description' => 'Systemprotokolldateien löschen',
            'success_msg' => 'Das Systemprotokoll wurde gelöscht',
        ],
    ],
];
