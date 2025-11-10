/* checkout-core.js
 * Gemeinsame Logik für Hotel & Kurse: Step-Navigation, Pflichtfelder, AGB-Check
 * Robust gegen parallele jQuery-Handler dank Capture-Phase im Submit.
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('.payment-checkout-form');
    if (!form) return;

    // --- STEP UI ---
    var panels = Array.prototype.slice.call(document.querySelectorAll('.step-panel'));
    var titleEl = document.getElementById('stepTitle');
    var dots = function (i) { return document.querySelector('[data-step-dot="' + i + '"]'); };
    var lines = function (i) { return document.querySelector('[data-step-line="' + i + '"]'); };
    var TITLES = ['Allgemeine Informationen', 'Ihre Angaben', 'Zahlung & Abschluss'];
    var step = parseInt(form.getAttribute('data-start-step') || '1', 10);
    if (isNaN(step) || step < 1 || step > 3) step = 1;

    render(step);

    // --- HELPERS ---
    var fieldIds = {
      first: 'txt-first_name',
      last:  'txt-last_name',
      email: 'txt-email',
      phone: 'txt-phone',
    };
    var getVal = function (id) {
      var el = document.getElementById(id);
      return (el && el.value ? el.value.trim() : '');
    };
    var emailOk = function (s) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(s); };
    var phoneOk = function (s) { return /^[0-9+\s\-()]+$/.test(s); };
    var markInvalid = function (el, isBad) { if (el) el.classList.toggle('is-invalid', !!isBad); };

    function showAlert(stepNo, msg) {
      var id  = stepNo === 2 ? 'formAlertStep2' : 'formAlertStep3';
      var box = document.getElementById(id);
      if (!box) return;
      box.textContent = msg || '';
      box.style.display = msg ? 'block' : 'none';
      if (msg) box.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function clearAlerts() {
      ['formAlertStep2', 'formAlertStep3'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) { el.style.display = 'none'; el.textContent = ''; }
      });
    }

    function validateStep(stepNo) {
      var errors = [];

      if (stepNo >= 2) {
        var fF = document.getElementById(fieldIds.first);
        var fL = document.getElementById(fieldIds.last);
        var fE = document.getElementById(fieldIds.email);
        var fP = document.getElementById(fieldIds.phone);

        [fF, fL, fE, fP].forEach(function (el) { markInvalid(el, false); });

        var vF = getVal(fieldIds.first);
        var vL = getVal(fieldIds.last);
        var vE = getVal(fieldIds.email);
        var vP = getVal(fieldIds.phone);

        if (!vF) errors.push(['Vorname ist erforderlich.', fF]);
        if (!vL) errors.push(['Nachname ist erforderlich.', fL]);
        if (!vE) errors.push(['E-Mail ist erforderlich.', fE]);
        else if (!emailOk(vE)) errors.push(['Bitte gültige E-Mail-Adresse angeben.', fE]);
        if (!vP) errors.push(['Telefon ist erforderlich.', fP]);
        else if (!phoneOk(vP)) errors.push(['Bitte gültige Telefonnummer angeben.', fP]);
        else if (vP.replace(/\D/g, '').length < 8) errors.push(['Telefonnummer muss min. 8 Ziffern enthalten.', fP]);

        errors.forEach(function (pair) { markInvalid(pair[1], true); });
      }

      if (stepNo === 3) {
        var terms = document.getElementById('terms_conditions');
        if (!terms || !terms.checked) errors.push(['Bitte akzeptieren Sie die Allgemeinen Geschäftsbedingungen.', terms]);
      }

      showAlert(stepNo, errors.length ? '⚠️ ' + errors[0][0] : '');
      return errors.length === 0;
    }

    // --- NAVIGATION ---
    form.addEventListener('click', function (e) {
      var next = e.target.closest('[data-next]');
      var prev = e.target.closest('[data-prev]');

      if (next) {
        e.preventDefault();
        clearAlerts();
        if (step === 1) { step = 2; render(step); return; }
        if (step === 2 && validateStep(2)) { step = 3; render(step); return; }
      }

      if (prev) {
        e.preventDefault();
        clearAlerts();
        step = Math.max(1, step - 1);
        render(step);
      }
    });

    // --- SUBMIT (Capture-Phase -> blockt vor jQuery/Payment) ---
    form.addEventListener('submit', function (e) {
      clearAlerts();
      var ok = validateStep(2) && validateStep(3);
      if (!ok) { e.preventDefault(); return false; }
    }, true);

    function render(s) {
      panels.forEach(function (p) { p.classList.toggle('active', p.getAttribute('data-step') == String(s)); });
      if (titleEl) titleEl.textContent = TITLES[s - 1];
      [1, 2, 3].forEach(function (i) {
        var d = dots(i), l = lines(i);
        if (d) d.classList.toggle('active', i <= s);
        if (l) l.classList.toggle('active', i <  s);
      });
      var c = document.querySelector('#couponBox .collapse');
      if (c && !c.classList.contains('show')) c.classList.add('show');
    }
  });
})();
