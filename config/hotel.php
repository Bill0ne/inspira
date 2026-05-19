<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Öffnungszeiten für Raumbuchungen
    |--------------------------------------------------------------------------
    |
    | Innerhalb dieses Zeitfensters dürfen Slots gebucht / angefragt werden.
    | Format: 24-Stunden "HH:MM". Inklusive Grenzen (start <= slot, slot <= end).
    | Werte stammen primär aus .env (HOTEL_OPENING_START / HOTEL_OPENING_END).
    |
    */

    'opening_hours' => [
        'start' => env('HOTEL_OPENING_START', '10:00'),
        'end' => env('HOTEL_OPENING_END', '21:00'),
    ],

];
