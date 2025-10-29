# Aktualisierung der Kurs-Session-Styles nach einem Deploy

Wenn nach einem Deploy weiterhin die alte Gestaltung der Kurslisten erscheint, sind in der Regel noch zwischengespeicherte Assets oder Ansichten aktiv. Folgende Schritte sollten unmittelbar nach dem Einspielen der Änderungen per SSH auf dem Server ausgeführt werden:

1. **Auf den Server verbinden und ins Projektverzeichnis wechseln.**
   ```bash
   cd /var/www/inspira
   ```
   (Pfad ggf. an eure Installation anpassen.)

2. **Aktuelle Plugin-Assets veröffentlichen.** Dadurch werden die neuen CSS-Dateien der Kursverwaltung in `public/vendor/` geschrieben.
   ```bash
   php artisan cms:publish:assets --force
   ```

3. **Zwischengespeicherte Views und Caches leeren.** So wird sichergestellt, dass keine alten Blade-Templates oder konfigurationsabhängigen Daten geladen werden.
   ```bash
   php artisan view:clear
   php artisan cache:clear
   php artisan config:clear
   ```

4. **Optional: Optimierungs-Caches zurücksetzen**, falls zuvor `php artisan optimize` ausgeführt wurde.
   ```bash
   php artisan optimize:clear
   ```

5. **Browser-Cache leeren bzw. mit Hard-Reload testen** (Strg/⌘ + Shift + R), damit auch lokal keine alten Styles mehr verwendet werden.

Nach diesen Schritten werden die aktualisierten Styles im Admin-Interface geladen. Sollte weiterhin das alte Layout sichtbar sein, prüfen ob die veröffentlichte Datei `public/vendor/core/plugins/courses/css/course-session-table.css` das aktuelle Änderungsdatum trägt und ob ein CDN oder Reverse-Proxy zusätzliche Caches vorhält.
