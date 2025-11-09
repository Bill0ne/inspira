<?php

return [
    'statuses' => [
        'draft' => 'Entwurf',
        'pending' => 'Ausstehend',
        'published' => 'Veröffentlicht',
    ],
    'system_updater_steps' => [
        'download' => 'Aktualisierungsdateien herunterladen',
        'update_files' => 'Systemdateien aktualisieren',
        'update_database' => 'Datenbanken aktualisieren',
        'publish_core_assets' => 'Core-Assets veröffentlichen',
        'publish_packages_assets' => 'Paket-Assets veröffentlichen',
        'clean_up' => 'Update-Dateien des Systems bereinigen',
        'done' => 'System erfolgreich aktualisiert',

        'messages' => [
            'download' => 'Update-Dateien werden heruntergeladen...',
            'update_files' => 'Systemdateien werden aktualisiert...',
            'update_database' => 'Datenbanken werden aktualisiert...',
            'publish_core_assets' => 'Core-Assets werden veröffentlicht...',
            'publish_packages_assets' => 'Paket-Assets werden veröffentlicht...',
            'clean_up' => 'Update-Dateien des Systems werden bereinigt...',
            'done' => 'Fertig! Der Browser wird in 30 Sekunden aktualisiert.',
        ],

        'failed_messages' => [
            'download' => 'Update-Dateien konnten nicht heruntergeladen werden',
            'update_files' => 'Systemdateien konnten nicht aktualisiert werden',
            'update_database' => 'Datenbanken konnten nicht aktualisiert werden',
            'publish_core_assets' => 'Core-Assets konnten nicht veröffentlicht werden',
            'publish_packages_assets' => 'Paket-Assets konnten nicht veröffentlicht werden',
            'clean_up' => 'Update-Dateien des Systems konnten nicht bereinigt werden',
        ],

        'success_messages' => [
            'download' => 'Update-Dateien erfolgreich heruntergeladen.',
            'update_files' => 'Systemdateien erfolgreich aktualisiert.',
            'update_database' => 'Datenbanken erfolgreich aktualisiert.',
            'publish_core_assets' => 'Core-Assets erfolgreich veröffentlicht.',
            'publish_packages_assets' => 'Paket-Assets erfolgreich veröffentlicht.',
            'clean_up' => 'Update-Dateien des Systems erfolgreich bereinigt.',
        ],
    ],
];
