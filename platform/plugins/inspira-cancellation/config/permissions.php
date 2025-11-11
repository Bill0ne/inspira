<?php

return [
    [
        'name' => 'Stornierung & Ersatzteilnehmer',
        'flag' => 'plugin.inspira-cancellation',
    ],
    [
        'name' => 'Übersicht',
        'flag' => 'inspira-cancellation.cancellations.index',
        'parent_flag' => 'plugin.inspira-cancellation',
    ],
    [
        'name' => 'Regeln verwalten',
        'flag' => 'inspira-cancellation.rules.index',
        'parent_flag' => 'plugin.inspira-cancellation',
    ],
    [
        'name' => 'Regel erstellen',
        'flag' => 'inspira-cancellation.rules.create',
        'parent_flag' => 'inspira-cancellation.rules.index',
    ],
    [
        'name' => 'Regel bearbeiten',
        'flag' => 'inspira-cancellation.rules.edit',
        'parent_flag' => 'inspira-cancellation.rules.index',
    ],
    [
        'name' => 'Regel löschen',
        'flag' => 'inspira-cancellation.rules.destroy',
        'parent_flag' => 'inspira-cancellation.rules.index',
    ],
    [
        'name' => 'Ersatzteilnehmer',
        'flag' => 'inspira-cancellation.transfers.index',
        'parent_flag' => 'plugin.inspira-cancellation',
    ],
];
