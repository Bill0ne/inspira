# Kurs-Bewertungen: Feature-Audit

## Wann ist eine Bewertung möglich?

Eine Kurs-Bewertung ist nur verfügbar, wenn die globale Einstellung `course_enable_review_room` aktiv ist (`true`/`1`). Ist sie deaktiviert, werden die Frontend-Routen für Kurs-Bewertungen nicht registriert und der Controller liefert zusätzlich 404, falls dennoch ein Request kommt.

Zusätzlich zeigt das Frontend den aktiven Submit-Button nur dann an, wenn ein Kunde:

1. eingeloggt ist,
2. den Kurs gebucht hat,
3. den Kurs noch nicht bewertet hat.

## Was passiert bei einer Bewertung?

Beim Absenden wird ein AJAX-POST an `customer/ajax/course/review/{key}` gesendet.

Server-seitig passiert folgendes:

1. Validierung (`course_id`, `star` 1..5, `content` min. 4 Zeichen),
2. Anlage eines `course_reviews`-Datensatzes,
3. Auslösen eines `CreatedContentEvent` mit `REVIEW_MODULE_SCREEN_NAME`,
4. Rückgabe einer Success-Message + aktualisierter Review-Anzahl.

Frontend-seitig passiert folgendes:

1. Button wird während des Requests deaktiviert,
2. bei Erfolg wird die Review-Anzahl aktualisiert,
3. Textarea + Button bleiben deaktiviert,
4. die Review-Liste wird neu geladen,
5. Erfolgsmeldung wird angezeigt.

## Wird der Kunde erinnert, eine Bewertung zu schicken?

Im aktuellen Code gibt es keine erkennbare Reminder-Logik für Kursbewertungen (kein Scheduler/Command/Notification-Flow, der nach Buchung automatisch um eine Bewertung bittet).

## Wann darf ein Kunde einen Kurs bewerten?

Nach UI- und Model-Logik darf ein Kunde bewerten, wenn er den Kurs bereits gebucht hat und noch keine nicht-abgelehnte Bewertung zu diesem Kurs besitzt.

Wichtig: Es gibt aktuell keine harte Prüfung auf "Kurs muss bereits vorbei sein" (z. B. Enddatum in der Vergangenheit). Die Berechtigung hängt nur an Buchung + noch nicht bewertet.

## Auffälligkeit (technische Lücke)

Die Store-Action prüft `canReviewCourse(...)` nur dann, wenn ein Kunde eingeloggt ist. Für nicht eingeloggte Requests greift diese Sperre nicht, obwohl das UI den Gast blockiert. Dadurch ist ein manueller POST grundsätzlich möglich, solange die Request-Validierung erfüllt ist.

Außerdem ist die `store`-Route nicht mit einem Auth-Middleware-Schutz versehen.

## Kurzfazit

- Fachlich laut UI: Bewertung nach Buchung genau einmal.
- Technisch laut Backend: Review-Feature ist schaltbar, schreibt direkt in `course_reviews` (Default-Status `approved`).
- Aktuell keine automatische Bewertungserinnerung.
- Sicherheits-/Regellücke: Gast-POST ist serverseitig nicht strikt ausgeschlossen.
