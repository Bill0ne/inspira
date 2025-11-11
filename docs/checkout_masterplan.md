# Inspira Checkout 2.0 Masterplan

## Zielsetzung
Ein schlanker, fehlerfreier, dreistufiger Checkout-Flow, der Vertrauen schafft, intuitiv ist und ohne Ablenkungen funktioniert. Der Flow unterscheidet klar zwischen Kurs- und Raum-Buchungen: Kurse enthalten Gutschein- und Zahlungsmodul, Räume fokussieren auf Zusatzleistungen ohne Gutscheinlogik.

## Seitenaufbau und Layoutstruktur
- **Topbar:** Fixierter, dezenter Streifen (#F8F8F8 Hintergrund, #17463F Text, 14px) mit Pfeil-Icon und Text "Zurück zur Übersicht", verlinkt zur jeweiligen Kurs- oder Raumübersicht.
- **Ticket-Header:** Bild links, Details mittig, Preis rechts; keine Hauptnavigation oder Footer.
- **Stepper mit drei Schritten:**
  1. **Zugang:** Optionen "Als Gast buchen" (Primär), "Einloggen" (Modal), "Registrieren" (Inline-Formular mit Name, E-Mail, Passwort, optionaler Checkbox "gleichzeitig buchen").
  2. **Angaben:** Persönliche Daten, Services/Foods bei Raum-Buchungen; autofill für eingeloggte Nutzer, clientseitige Validierung, dynamische Zusatzfelder nur bei Hotel.
  3. **Zahlung & Abschluss:** Übersicht, AGB-Bestätigung, Zahlungsarten; Gutschein-Feld nur bei Kursen; Payment-Reload nach Gutscheinanwendung; Abschluss-Button prüft AGB und Validierungen.

## Formularlogik und Validierung
- **Clientseitig:** Pflichtfelder (Vorname, Nachname, E-Mail, Telefon); Regex-Checks für E-Mail und Telefonnummer; Soft-Validation mit direktem Feedback.
- **Serverseitig:** Laravel `CheckoutRequest` validiert Pflichtfelder erneut.
- **AGB:** Checkbox zwingend; Warnung und kein Submit ohne Zustimmung.

## Coupon- und Payment-System
- **Kurse:** Gutschein-Feld mit "Anwenden"-Button; bei Erfolg sofortiger Rabatt, AJAX-Reload der Zahlarten; Entfernen setzt Rabatt und Feld zurück, Totals aktualisieren sich ohne Reload.
- **Räume:** Kein Gutschein-Feld; Preisaktualisierung ausschließlich durch Zusatzleistungen (Service/Food).

## Technische Struktur
- **JavaScript-Module:**
  - `checkout-core.js`: Steuernavigation, Validierung, AGB-Check.
  - `checkout-commerce.js`: Coupon- und Payment-Handling für Kurse.
  - `checkout-hotel.js`: Zusatzleistungen und Preiskalkulation für Räume.
  - `checkout-init.js`: Initialisierung und Aktivierung je nach Szenario.
- **Asset-Einbindung:**
  ```blade
  @if ($isCourse)
    Theme::asset()->container('footer')->usePath()->add('checkout-core', 'js/checkout-core.js');
    Theme::asset()->container('footer')->usePath()->add('checkout-commerce', 'js/checkout-commerce.js');
  @else
    Theme::asset()->container('footer')->usePath()->add('checkout-core', 'js/checkout-core.js');
    Theme::asset()->container('footer')->usePath()->add('checkout-hotel', 'js/checkout-hotel.js');
  @endif
  ```

## Designprinzipien
- Primärfarbe #578E88, klare Kontraste (#000/#FFF), großzügige Weißräume.
- Primär-Buttons in Vollbreite, Sekundär-Buttons als Mint-Outline.
- Sanfte Fade/Slide-Animationen beim Step-Wechsel.
- Inline-Feedback statt Pop-ups; Toastr ausschließlich für Gutscheine.

## Fehlerbehandlung und UX-Sicherheit
- Inline-Fehlermeldungen mit Scroll-to-Error.
- Serverfehler (z. B. Payment) als Toast, kein Reload.
- Ungültige Gutscheine: Roter Hinweis unter dem Feld plus Reset.
- Warnung bei Browser-Zurück, dass der Vorgang abgebrochen wird.

## UX-Besonderheiten
- Persistenz via `localStorage` verhindert Datenverlust bei Reload.
- Auto-Prefill für eingeloggte Nutzer über `auth()->user()`.
- Stepper als visuelle Progress-Bar.
- Enter-Taste löst keine unbeabsichtigten Navigationswechsel aus.

## Technisches Fazit
- **Architektur:** Modularisierte JS-Layer (Core/Commerce/Hotel).
- **Fehlerhandling:** Submit-Block auf Basis von Validierungs-Captures.
- **Styles:** SCSS-Modularisierung in `assets/sass/checkout.scss`.
- **Kompatibilität:** Unterstützung für Payment-Plugins (Stripe, PayPal, Mollie).
- **Migration:** Abwärtskompatibel zur bestehenden Datenstruktur.

## Ergebnis
Ein Checkout, der fehlerfrei validiert, dynamisch auf Kurse und Räume reagiert, visuell reduziert und vertrauensbildend ist, Coupons nahtlos integriert und auf einem sauberen 3-Layer-Modell basiert.
