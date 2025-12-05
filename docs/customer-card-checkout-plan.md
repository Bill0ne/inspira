# Checkout-Analyse Kundenkarten

## Fehlerbild
* Sporadische Fehlermeldung von Stripe: `The "line_items" parameter is required in payment mode.`
* Tritt auf, wenn die Payment-Payload ohne Produktpositionen an Stripe gesendet wird.

## Ursachenanalyse
1. **Zerfall der Checkout-Daten**
   * Der `PAYMENT_FILTER_PAYMENT_DATA` Fallback für `CustomerCardOrder` erzeugte leere `products`, sobald kein gültiger Auftrag in der Anfrage gefunden wurde.
   * Stripe verlangt bei Checkout-Sessions zwingend mindestens ein `line_item`; ohne Produktliste bricht die API mit obiger Meldung ab.
2. **Fehlende Vorab-Validierung**
   * Der Stripe-Gateway-Service leitete die API-Anfrage weiter, auch wenn `line_items` leer waren. Dadurch erschien der Fehler erst als Stripe-Fehlerseite.

## Umgesetzte Stabilisierung
* **Fallback-Produkt für Kundenkarten**: Auch wenn ein Pending-Order-Datensatz nicht geladen werden kann, wird jetzt ein generisches Produkt (Preis = angeforderter Betrag, Menge = 1) erzeugt. So bleibt die Produktliste konsistent und Stripe erhält immer mindestens einen `line_item`.
* **Guard im Stripe-Gateway**: Bevor eine Checkout-Session erstellt wird, prüft der Service, ob überhaupt `line_items` vorhanden sind. Fehlen sie, wird die Anfrage lokal abgebrochen und aussagekräftig geloggt, statt eine fehlerhafte Stripe-Anfrage zu senden.

## Weiterführende Empfehlungen
* Formular-Requests beim Kauf einer Kundenkarte serverseitig validieren (z. B. `order_id` Pflicht, Besitzprüfung), damit der Fallback nur als Netz-/Session-Sicherheit dient.
* Log-Monitoring auf den neuen Warnhinweis setzen (`Stripe checkout aborted: missing line items`), um fehlerhafte Flows früh zu erkennen.
* E2E-Test für den Kundenkarten-Checkout ergänzen, der eine Session ohne `order_id` simuliert und sicherstellt, dass kein Stripe-Call ohne Produkte erfolgt.
