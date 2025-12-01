# Kundenkarten-Verbrauchsanalyse

## Wann wird eine Verwendung abgezogen?

### Kurs-Buchungen (CustomerCardPricingService::finalizeUsage)
* Die eigentliche Reduzierung der `units_remaining` und das Anlegen eines Eintrags in `ht_customer_card_usages` passieren in `Botble\Hotel\Services\CustomerCardPricingService::finalizeUsage()`. Dabei wird eine bereits angelegte Buchung (`CourseBooking`) verarbeitet und auf doppelte Verbuchung geprüft. Anschließend wird innerhalb einer Datenbank-Transaktion die Karte gesperrt und der Verbrauch aufgezeichnet.
* Der Abzug läuft nur, wenn `customer_card_id` gesetzt ist und mindestens eine Einheit (`customer_card_units_used`) hinterlegt wurde.

### Auslöser für den Abzug (CourseBookingService::finalizeCustomerCardUsage)
* `CourseBookingService::finalizeCustomerCardUsage()` entscheidet, ob der Abzug ausgeführt wird und ruft dafür `CustomerCardPricingService::finalizeUsage()` auf.
* Schutzmechanismen: ohne gespeicherte Buchung, ohne hinterlegte Zahlung bei positiven Beträgen oder wenn ein Payment noch nicht `COMPLETED` ist, wird der Abzug übersprungen (ggf. als "pending" markiert).
* Bei Buchungen mit Kartenbeteiligung, aber ohne fertige Zahlung und Betrag `0`, wird trotzdem finalisiert, um reine Karten-Transaktionen abzudecken.

### Ereignisse und Controller, die den Abzug triggern
* **Checkout bei Betrag 0 (volle Kartendeckung)**: `Botble\Courses\Http\Controllers\PublicController` legt eine Zahlung mit Betrag `0` an, ruft `do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, …)` auf und führt direkt `finalizeCustomerCardUsage()` aus.
* **Checkout mit Restzahlung online**: derselbe Controller legt die Buchung mit Kartenrabatt an, leitet in den Zahlungsprozess und verlässt sich auf spätere Events.
* **Buchung erstellt (Event)**: `Botble\Courses\Providers\HookServiceProvider` lauscht auf `CourseBookingCreated` und ruft `finalizeCustomerCardUsage()`, sofern keine Zahlung erforderlich ist oder noch fehlt.
* **Zahlung wurde aktualisiert**: derselbe Provider verarbeitet `ACTION_AFTER_UPDATE_PAYMENT`; sobald die Zahlung `COMPLETED` ist und eine Karte hinterlegt wurde, wird `finalizeCustomerCardUsage()` aufgerufen.
* **Manuelle Payment-Synchronisation**: bei `ECOMMERCE_ORDER_AFTER_PRODUCT_RETURNED`/`PAYMENT_ACTION_PAYMENT_PROCESSED` Hooks (gleicher Provider) wird die Buchung erneut geladen und finalisiert, falls nötig.

## Schwachstellen und logische Risiken
* **Entkoppelte Restzahlung**: Wenn ein Payment-Provider den Status nicht auf `COMPLETED` hebt (z. B. Abbruch nach Autorisierung), bleibt `finalizeCustomerCardUsage()` im Pending-Zustand und der Karteneinsatz wird nie abgezogen, obwohl der Kurs vielleicht geliefert wird.
* **Falscher Karteninhaber**: Die Finalisierung wird abgebrochen, wenn `assigned_to` nicht zum Buchungskunden passt. Dadurch kann eine Buchung mit Kartenrabatt durchlaufen, ohne dass Einheiten abgezogen werden, sofern die Zuordnung später korrigiert wird.
* **Mehrfache Einheitenermittlung**: `customer_card_units_used` wird bei der Finalisierung auf mindestens `1` gesetzt, unabhängig davon, ob zuvor ein anderer Wert kalkuliert wurde. Eine spätere Änderung der Preiskalkulation (z. B. mehr Einheiten pro Kurs) würde nicht berücksichtigt.
* **Rabatt ohne Abzug**: Wenn der Abzug wegen Zahlungsstatus fehlschlägt, bleibt der auf der Buchung gespeicherte Rabatt erhalten. Damit kann der Kunde einen vergünstigten Preis zahlen, ohne dass Einheiten verbraucht werden.
* **Teilzahlungen / Splits**: Felder wie `payment_split_card_gross` und `payment_split_online_gross` existieren, werden aber im Finalisierungsfluss nicht genutzt. Dadurch gibt es keine belastbare Trennung, wie viel über Karte bzw. extern bezahlt wurde – Risiken bei Reports oder Rückerstattungen.
* **Keine erneute Finalisierung bei späterem Kartentausch**: Wird eine andere Karte nachträglich zugeordnet, gibt es keinen Mechanismus, einen vorhandenen Usage-Eintrag zu löschen/ersetzen und neu zu verbuchen.

## Konzept für robuste Umsetzung (Follow-up)
1. Payment-Status-Überwachung
   * Sicherstellen, dass jeder Zahlungsabschluss (auch manuell bestätigte/offline Zahlungen) einen eindeutigen Callback triggert, der `finalizeCustomerCardUsage()` erneut versucht.
2. Zwingende Finalisierung bei Preisreduktion
   * Wenn eine Buchung Rabatte aus Karten enthält, darf der Kurszugang erst nach erfolgreicher Finalisierung freigegeben werden; andernfalls den Status nicht auf „PROCESSING“ setzen.
3. Konsistente Einheitenermittlung
   * `customer_card_units_used` früh und verbindlich im Checkout speichern (ggf. pro Kurskonfiguration) und in der Finalisierung nicht mehr auf `1` zurücksetzen.
4. Korrekturpfad für Kartenwechsel
   * Admin-/Support-Flow bereitstellen, um einen falschen Kartenbezug zu stornieren (Usage zurückbuchen) und korrekt neu zu verbuchen.
5. Teilzahlungen transparent machen
   * `payment_split_card_gross` / `payment_split_online_gross` bei der Berechnung befüllen und in Reports berücksichtigen, um Karteneinsätze sauber zu dokumentieren.
