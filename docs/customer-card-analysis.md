# Kundenkarten-Verbrauchsanalyse

## Wann wird eine Verwendung abgezogen?

### Schritt 1: Trigger sammeln
* **PublicController::postCheckout()**
  * Betrag == 0: erzeugt Payment mit Status `COMPLETED`, feuert `PAYMENT_ACTION_PAYMENT_PROCESSED` und ruft sofort `CourseBookingService::finalizeCustomerCardUsage()` auf.【F:platform/plugins/courses/src/Http/Controllers/PublicController.php†L548-L604】
  * Betrag > 0: legt Buchung & Payment an, verlässt sich auf spätere Events.【F:platform/plugins/courses/src/Http/Controllers/PublicController.php†L548-L679】
* **HookServiceProvider (Courses)**
  * `CourseBookingCreated` Event: finalisiert, wenn keine oder noch keine Zahlung nötig ist.【F:platform/plugins/courses/src/Providers/HookServiceProvider.php†L121-L137】
  * `PAYMENT_ACTION_PAYMENT_PROCESSED`: synchronisiert Payment, setzt Status und finalisiert Karten, falls vorhanden.【F:platform/plugins/courses/src/Providers/HookServiceProvider.php†L47-L118】
  * `ACTION_AFTER_UPDATE_PAYMENT`: bei Statusänderungen auf `COMPLETED` (auch COD/Banküberweisung) wird erneut finalisiert.【F:platform/plugins/courses/src/Providers/HookServiceProvider.php†L204-L267】

### Schritt 2: Entscheidung & Vorbedingungen (CourseBookingService::finalizeCustomerCardUsage)
* Stellt sicher, dass Buchung existiert, eine Karte hinterlegt ist und `customer_card_units_used > 0`.【F:platform/plugins/courses/src/Services/CourseBookingService.php†L109-L195】
* Prüft Payment-Status: solange Zahlung >0 und nicht `COMPLETED`, wird die Finalisierung auf „pending“ gesetzt und abgebrochen; bei 0-Euro-Buchungen wird ohne Completed-Status weitergemacht.【F:platform/plugins/courses/src/Services/CourseBookingService.php†L152-L188】
* Kümmert sich um Normalisierung von `payment_method`, `coverage_type` sowie Aufteilung `payment_split_card_gross`/`payment_split_online_gross`.【F:platform/plugins/courses/src/Services/CourseBookingService.php†L119-L238】
* Sperrt die Karte erneut (DB-Transaktion) und leitet in Schritt 3 über `CustomerCardPricingService::finalizeUsage()` weiter.【F:platform/plugins/courses/src/Services/CourseBookingService.php†L245-L279】

### Schritt 3: Verbrauch verbuchen (CustomerCardPricingService::finalizeUsage)
* Lädt die Karte `FOR UPDATE`, prüft Zuordnung zum Kunden und verhindert doppelte Verbuchung.【F:platform/plugins/hotel/src/Services/CustomerCardPricingService.php†L45-L92】
* Validiert angeforderte Einheiten (`customer_card_units_used`), korrigiert auf min. 1 und max. verfügbare Einheiten, zieht `units_remaining` ab und deaktiviert Karte bei 0.【F:platform/plugins/hotel/src/Services/CustomerCardPricingService.php†L93-L130】
* Legt `CustomerCardUsage` an, markiert die Buchung mit `customer_card_consumed_at`.【F:platform/plugins/hotel/src/Services/CustomerCardPricingService.php†L131-L152】

### Aktueller Ablauf (Timeline)
1. Buchung wird gespeichert (inkl. Kartenrabatt & angeforderten Einheiten).
2. Je nach Pfad (0-Euro-Checkout, Event `CourseBookingCreated`, Payment-Hooks) wird `finalizeCustomerCardUsage()` aufgerufen.
3. `finalizeCustomerCardUsage()` prüft Payment-Voraussetzungen und triggert `CustomerCardPricingService::finalizeUsage()`.
4. `finalizeUsage()` reduziert `units_remaining` und schreibt den Usage-Eintrag.

## Schwachstellen und logische Risiken
* **Entkoppelte Restzahlung**: Wenn ein Payment-Provider den Status nicht auf `COMPLETED` hebt (z. B. Abbruch nach Autorisierung), bleibt `finalizeCustomerCardUsage()` im Pending-Zustand und der Karteneinsatz wird nie abgezogen, obwohl der Kurs vielleicht geliefert wird.
* **Falscher Karteninhaber**: Die Finalisierung wird abgebrochen, wenn `assigned_to` nicht zum Buchungskkunden passt. Dadurch kann eine Buchung mit Kartenrabatt durchlaufen, ohne dass Einheiten abgezogen werden, sofern die Zuordnung später korrigiert wird.
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

## Vorschlag: Abzug konsistent nachgelagert auslösen
* Den Aufruf von `CourseBookingService::finalizeCustomerCardUsage()` gebündelt **nach erfolgreichem Speichern der Buchung & Payment-Infos** platzieren, z. B. direkt nach dem Rendern der Booking-Info-Ansicht oder in einem dedizierten „Post-Booking“-Handler.
* Ziel: derselbe Call-Punkt für 0-Euro- und Restzahlungs-Bookings, um den Abzug nicht mehr vorzeitig im Checkout-Controller oder mehrfach in Events auszuführen.
* Begleitmaßnahmen:
  * Die Payment-Hooks behalten, aber nur als **Retry-Mechanismus**, falls der nachgelagerte Call scheitert (z. B. wegen Payment-Status).
  * Sicherstellen, dass `booking-information` erst angezeigt wird, wenn entweder der Abzug erfolgreich war oder eindeutig „pending“ markiert ist, damit kein inkonsistenter Rabatt ohne Verbrauch verbleibt.
