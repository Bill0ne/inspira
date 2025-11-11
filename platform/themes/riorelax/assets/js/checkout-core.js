/* checkout-core.js
 * Gemeinsame Logik für Hotel & Kurse: Step-Navigation, Validierung, AGB-Check
 * + Login-Modal, Inline-Registrierung & LocalStorage-Persistenz.
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
    var ALERT_IDS = { 1: 'formAlertStep1', 2: 'formAlertStep2', 3: 'formAlertStep3' };
    var step = parseInt(form.getAttribute('data-start-step') || '1', 10);
    if (isNaN(step) || step < 1 || step > 3) step = 1;

    // --- HELFER ---
    var fieldIds = {
      first: 'txt-first_name',
      last: 'txt-last_name',
      email: 'txt-email',
      phone: 'txt-phone'
    };
    var getVal = function (id) {
      var el = document.getElementById(id);
      return (el && el.value ? el.value.trim() : '');
    };
    var emailOk = function (s) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(s); };
    var phoneOk = function (s) { return /^[0-9+\s\-()]+$/.test(s); };
    var markInvalid = function (el, isBad) { if (el) el.classList.toggle('is-invalid', !!isBad); };

    function showAlert(stepNo, msg) {
      var box = ALERT_IDS[stepNo] ? document.getElementById(ALERT_IDS[stepNo]) : null;
      if (!box) return;
      box.textContent = msg || '';
      box.style.display = msg ? 'block' : 'none';
      if (msg) box.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function clearAlerts(stepNo) {
      if (stepNo) {
        showAlert(stepNo, '');
        return;
      }
      Object.keys(ALERT_IDS).forEach(function (key) { showAlert(parseInt(key, 10), ''); });
    }

    // --- LOCAL STORAGE ---
    var storageKey = form.getAttribute('data-storage-key');
    var storageFields = ['first_name', 'last_name', 'email', 'phone', 'country', 'state', 'city', 'address', 'zip', 'requests', 'arrival_time'];
    var storedData = {};

    if (storageKey) {
      try {
        var raw = localStorage.getItem(storageKey);
        storedData = raw ? JSON.parse(raw) : {};
      } catch (err) {
        storedData = {};
      }
      if (typeof storedData !== 'object' || Array.isArray(storedData) || storedData === null) {
        storedData = {};
      }

      storageFields.forEach(function (name) {
        var el = form.querySelector('[name="' + name + '"]');
        if (!el) return;
        if (!el.value && Object.prototype.hasOwnProperty.call(storedData, name)) {
          if (el.type === 'checkbox') {
            el.checked = storedData[name] === '1';
          } else {
            el.value = storedData[name];
          }
        }
      });

      if (Object.keys(storedData).length === 0) {
        storageFields.forEach(function (name) {
          var el = form.querySelector('[name="' + name + '"]');
          if (el && el.value) {
            storedData[name] = el.value;
          }
        });
        try { localStorage.setItem(storageKey, JSON.stringify(storedData)); } catch (err) {}
      }
    }

    function persistField(name, value) {
      if (!storageKey) return;
      if (value === '' || value === null || typeof value === 'undefined') {
        delete storedData[name];
      } else {
        storedData[name] = value;
      }
      try { localStorage.setItem(storageKey, JSON.stringify(storedData)); } catch (err) {}
    }

    function handleFieldPersist(target) {
      if (!target || !target.name) return;
      if (storageFields.indexOf(target.name) === -1) return;
      var value = target.type === 'checkbox' ? (target.checked ? '1' : '') : target.value;
      persistField(target.name, value);
    }

    form.addEventListener('input', function (e) { handleFieldPersist(e.target); }, true);
    form.addEventListener('change', function (e) { handleFieldPersist(e.target); }, true);

    // --- INLINE REGISTRIERUNG ---
    var registerBox = form.querySelector('[data-register-box]');
    var registerActions = form.querySelector('[data-register-actions]');
    var registerFlag = document.getElementById('register_customer_flag');
    var registerInputs = {
      first: form.querySelector('input[name="register_first_name"]'),
      last: form.querySelector('input[name="register_last_name"]'),
      email: form.querySelector('input[name="register_email"]'),
      password: form.querySelector('input[name="password"]'),
      confirm: form.querySelector('input[name="password_confirmation"]')
    };

    function openRegister() {
      if (!registerBox) return;
      registerBox.classList.remove('d-none');
      if (registerActions) registerActions.classList.add('d-none');
      clearAlerts(1);
    }

    function closeRegister(clear) {
      if (!registerBox) return;
      registerBox.classList.add('d-none');
      if (registerActions) registerActions.classList.remove('d-none');
      if (clear !== false) {
        Object.keys(registerInputs).forEach(function (key) {
          var input = registerInputs[key];
          if (!input) return;
          if (input.type === 'checkbox') {
            input.checked = false;
          } else {
            input.value = '';
          }
          markInvalid(input, false);
        });
        if (registerFlag) registerFlag.value = '0';
      }
      clearAlerts(1);
    }

    function copyToStepTwo(name, value) {
      var target = form.querySelector('[name="' + name + '"]');
      if (!target) return;
      target.value = value;
      markInvalid(target, false);
      persistField(name, value);
      try { target.dispatchEvent(new Event('input', { bubbles: true })); } catch (err) {}
    }

    function handleRegisterContinue() {
      if (!registerBox) return;

      clearAlerts(1);
      var errors = [];
      var firstVal = (registerInputs.first && registerInputs.first.value || '').trim();
      var lastVal = (registerInputs.last && registerInputs.last.value || '').trim();
      var emailVal = (registerInputs.email && registerInputs.email.value || '').trim();
      var passVal = registerInputs.password ? registerInputs.password.value : '';
      var confirmVal = registerInputs.confirm ? registerInputs.confirm.value : '';

      Object.keys(registerInputs).forEach(function (key) { markInvalid(registerInputs[key], false); });

      if (!firstVal) errors.push({ message: 'Vorname ist erforderlich.', el: registerInputs.first });
      if (!lastVal) errors.push({ message: 'Nachname ist erforderlich.', el: registerInputs.last });
      if (!emailVal) errors.push({ message: 'E-Mail ist erforderlich.', el: registerInputs.email });
      else if (!emailOk(emailVal)) errors.push({ message: 'Bitte gültige E-Mail-Adresse angeben.', el: registerInputs.email });
      if (!passVal || passVal.length < 6) errors.push({ message: 'Passwort muss mindestens 6 Zeichen enthalten.', el: registerInputs.password });
      if (passVal !== confirmVal) errors.push({ message: 'Passwörter stimmen nicht überein.', el: registerInputs.confirm });

      if (errors.length) {
        errors.forEach(function (err) { markInvalid(err.el, true); });
        showAlert(1, '⚠️ ' + errors[0].message);
        return;
      }

      if (registerFlag) registerFlag.value = '1';
      copyToStepTwo('first_name', firstVal);
      copyToStepTwo('last_name', lastVal);
      copyToStepTwo('email', emailVal);

      step = 2;
      render(step);
      clearAlerts(1);
    }

    if (form.getAttribute('data-start-register') === '1') {
      openRegister();
    }

    // --- LOGIN-MODAL ---
    var loginModal = document.getElementById('checkoutLoginModal');

    function openLoginModal() {
      if (!loginModal) return;
      loginModal.classList.add('is-visible');
      loginModal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('checkout-modal-open');
      var emailField = loginModal.querySelector('input[name="email"]');
      if (emailField) {
        try { emailField.focus({ preventScroll: true }); } catch (err) { emailField.focus(); }
      }
    }

    function closeLoginModal() {
      if (!loginModal) return;
      loginModal.classList.remove('is-visible');
      loginModal.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('checkout-modal-open');
    }

    document.addEventListener('click', function (e) {
      if (e.target.closest('[data-open-login]')) {
        e.preventDefault();
        openLoginModal();
        return;
      }
      if (e.target.closest('[data-close-login]')) {
        e.preventDefault();
        closeLoginModal();
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        closeLoginModal();
      }
    });

    // --- NAVIGATION & EVENTS ---
    form.addEventListener('click', function (e) {
      var next = e.target.closest('[data-next]');
      var prev = e.target.closest('[data-prev]');
      var registerToggleBtn = e.target.closest('[data-toggle-register]');
      var registerCancelBtn = e.target.closest('[data-cancel-register]');
      var registerNextBtn = e.target.closest('[data-register-next]');

      if (registerToggleBtn) {
        e.preventDefault();
        openRegister();
        return;
      }

      if (registerCancelBtn) {
        e.preventDefault();
        closeRegister(true);
        return;
      }

      if (registerNextBtn) {
        e.preventDefault();
        handleRegisterContinue();
        return;
      }

      if (next) {
        e.preventDefault();
        clearAlerts();
        if (step === 1) {
          closeRegister(true);
          step = 2;
          render(step);
          return;
        }
        if (step === 2 && validateStep(2)) {
          step = 3;
          render(step);
          return;
        }
      }

      if (prev) {
        e.preventDefault();
        clearAlerts();
        step = Math.max(1, step - 1);
        render(step);
      }
    });

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

    // --- SUBMIT (Capture-Phase -> blockt vor jQuery/Payment) ---
    form.addEventListener('submit', function (e) {
      clearAlerts();
      var ok = validateStep(2) && validateStep(3);
      if (!ok) {
        e.preventDefault();
        return false;
      }
      if (storageKey) {
        try { localStorage.removeItem(storageKey); } catch (err) {}
      }
      return true;
    }, true);

    function render(s) {
      panels.forEach(function (p) { p.classList.toggle('active', p.getAttribute('data-step') == String(s)); });
      if (titleEl && TITLES[s - 1]) titleEl.textContent = TITLES[s - 1];
      [1, 2, 3].forEach(function (i) {
        var d = dots(i), l = lines(i);
        if (d) d.classList.toggle('active', i <= s);
        if (l) l.classList.toggle('active', i < s);
      });
      var c = document.querySelector('#couponBox .collapse');
      if (c && !c.classList.contains('show')) c.classList.add('show');
    }

    render(step);
  });
})();
