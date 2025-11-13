document.addEventListener('DOMContentLoaded', function() {
  // Verwende capture-phase, damit dieser Handler VOR jQuerys Handler greift
  document.addEventListener('submit', function(e) {
    const form = e.target.closest('.payment-checkout-form');
    if (!form) return;

    // Terms & Conditions prüfen
    const terms = form.querySelector('#terms_conditions');
    if (!terms || !terms.checked) {
      e.preventDefault();
      const alertBox = document.getElementById('formAlertStep3');
      if (alertBox) {
        alertBox.textContent = '⚠️ Bitte akzeptieren Sie die Allgemeinen Geschäftsbedingungen, um fortzufahren.';
        alertBox.style.display = 'block';
        alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
      } else {
        alert('Bitte akzeptieren Sie die AGB, um fortzufahren.');
      }
      return false;
    }

    // Step 2 Validierung (bei Gastbuchung)
    const step2Fields = ['#txt-first_name', '#txt-last_name', '#txt-email', '#txt-phone'];
    let hasError = false;

    for (const selector of step2Fields) {
      const el = form.querySelector(selector);
      if (!el) continue;
      const value = el.value.trim();
      if (!value) {
        el.classList.add('is-invalid');
        hasError = true;
      } else {
        el.classList.remove('is-invalid');
      }
    }

    if (hasError) {
      e.preventDefault();
      const alert2 = document.getElementById('formAlertStep2');
      if (alert2) {
        alert2.textContent = '⚠️ Bitte füllen Sie alle Pflichtfelder korrekt aus.';
        alert2.style.display = 'block';
        alert2.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
      return false;
    }

  }, true); // capture-phase!
});
