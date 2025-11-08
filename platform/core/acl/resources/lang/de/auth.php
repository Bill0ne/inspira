<?php

return [
    'login' => [
        'username' => 'E-Mail/Benutzername',
        'email' => 'E-Mail',
        'password' => 'Passwort',
        'title' => 'Benutzeranmeldung',
        'remember' => 'Angemeldet bleiben?',
        'login' => 'Anmelden',
        'placeholder' => [
            'username' => 'Benutzername oder E-Mail-Adresse eingeben',
            'email' => 'E-Mail-Adresse eingeben',
            'password' => 'Passwort eingeben',
        ],
        'success' => 'Erfolgreich angemeldet!',
        'fail' => 'Falscher Benutzername oder falsches Passwort.',
        'not_active' => 'Dein Konto wurde noch nicht aktiviert!',
        'banned' => 'Dieses Konto ist gesperrt.',
        'logout_success' => 'Erfolgreich abgemeldet!',
        'dont_have_account' => 'Du hast noch kein Konto in diesem System. Bitte kontaktiere die Verwaltung für weitere Informationen!',
    ],
    'forgot_password' => [
        'title' => 'Passwort vergessen',
        'message' => '<p>Passwort vergessen?</p><p>Bitte gib deine E-Mail-Adresse ein. Das System sendet dir einen Link, mit dem du dein Passwort zurücksetzen kannst.</p>',
        'submit' => 'Absenden',
    ],
    'reset' => [
        'new_password' => 'Neues Passwort',
        'password_confirmation' => 'Neues Passwort bestätigen',
        'email' => 'E-Mail',
        'title' => 'Passwort zurücksetzen',
        'update' => 'Aktualisieren',
        'wrong_token' => 'Dieser Link ist ungültig oder abgelaufen. Bitte verwende das Formular erneut.',
        'user_not_found' => 'Dieser Benutzername existiert nicht.',
        'success' => 'Passwort erfolgreich zurückgesetzt!',
        'fail' => 'Token ist ungültig, der Link zum Zurücksetzen ist abgelaufen!',
        'reset' => [
            'title' => 'E-Mail zum Zurücksetzen des Passworts',
        ],
        'send' => [
            'success' => 'Eine E-Mail wurde an dein Konto gesendet. Bitte prüfe dein Postfach.',
            'fail' => 'E-Mail konnte derzeit nicht gesendet werden. Bitte versuche es später erneut.',
        ],
        'new-password' => 'Neues Passwort',
        'placeholder' => [
            'new_password' => 'Neues Passwort eingeben',
            'new_password_confirmation' => 'Neues Passwort bestätigen',
        ],
    ],
    'email' => [
        'reminder' => [
            'title' => 'E-Mail zum Zurücksetzen des Passworts',
        ],
    ],
    'password_confirmation' => 'Passwortbestätigung',
    'failed' => 'Fehlgeschlagen',
    'throttle' => 'Zu viele Versuche',
    'not_member' => 'Noch kein Mitglied?',
    'register_now' => 'Jetzt registrieren',
    'lost_your_password' => 'Passwort vergessen?',
    'login_title' => 'Admin',
    'login_via_social' => 'Über soziale Netzwerke anmelden',
    'back_to_login' => 'Zurück zur Anmeldung',
    'sign_in_below' => 'Hier anmelden',
    'languages' => 'Sprachen',
    'reset_password' => 'Passwort zurücksetzen',
    'settings' => [
        'email' => [
            'title' => 'ACL',
            'description' => 'E-Mail-Konfiguration für ACL',
            'templates' => [
                'password_reminder' => [
                    'title' => 'Passwort zurücksetzen',
                    'description' => 'E-Mail an Benutzer senden, wenn ein Passwort-Reset angefordert wird',
                    'subject' => 'Passwort zurücksetzen',
                    'reset_link' => 'Link zum Zurücksetzen des Passworts',
                ],
            ],
        ],
    ],
];
