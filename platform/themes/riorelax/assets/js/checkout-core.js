/**
 * checkout-core.js
 * Steps + Pflichtfelder + AGB-Check (für Hotel & Kurse)
 */
document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('.payment-checkout-form');
  if (!form) return;

  const panels = [...document.querySelectorAll('.step-panel')];
  const title = document.getElementById('stepTitle');
  const dots  = i => document.querySelector(`[data-step-dot="${i}"]`);
  const lines = i => document.querySelector(`[data-step-line="${i}"]`);
  const titles = ['Allgemeine Informationen','Ihre Angaben','Zahlung & Abschluss'];
  let step = parseInt(form.dataset.startStep || '1', 10);

  render(step);

  const val = id => (document.getElementById(id)?.value.trim() || '');
  const emailOk = s => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(s);
  const phoneOk = s => /^[0-9+\s\-()]+$/.test(s);
  const markInvalid = (el, bad) => el && el.classList.toggle('is-invalid', !!bad);

  function showAlert(stepNo, msg){
    const id = stepNo === 2 ? 'formAlertStep2' : 'formAlertStep3';
    const box = document.getElementById(id);
    if (!box) return;
    box.textContent = msg || '';
    box.style.display = msg ? 'block' : 'none';
    if (msg) box.scrollIntoView({ behavior:'smooth', block:'center' });
  }
  function clearAlerts(){
    ['formAlertStep2','formAlertStep3'].forEach(id=>{
      const el = document.getElementById(id);
      if (el) { el.style.display='none'; el.textContent=''; }
    });
  }

  function validateStep(stepNo){
    const fields = {
      first: 'txt-first_name',
      last:  'txt-last_name',
      email: 'txt-email',
      phone: 'txt-phone',
    };
    const errors = [];

    if (stepNo >= 2) {
      const fF = document.getElementById(fields.first);
      const fL = document.getElementById(fields.last);
      const fE = document.getElementById(fields.email);
      const fP = document.getElementById(fields.phone);
      const vF = val(fields.first);
      const vL = val(fields.last);
      const vE = val(fields.email);
      const vP = val(fields.phone);

      markInvalid(fF,false); markInvalid(fL,false); markInvalid(fE,false); markInvalid(fP,false);

      if (!vF) errors.push(['Vorname ist erforderlich.', fF]);
      if (!vL) errors.push(['Nachname ist erforderlich.', fL]);
      if (!vE) errors.push(['E-Mail ist erforderlich.', fE]);
      else if (!emailOk(vE)) errors.push(['Bitte gültige E-Mail-Adresse angeben.', fE]);
      if (!vP) errors.push(['Telefon ist erforderlich.', fP]);
      else if (!phoneOk(vP)) errors.push(['Bitte gültige Telefonnummer angeben.', fP]);
      else if (vP.replace(/\D/g,'').length < 8) errors.push(['Telefonnummer muss min. 8 Ziffern enthalten.', fP]);

      errors.forEach(([_, el]) => markInvalid(el, true));
    }

    if (stepNo === 3) {
      const terms = document.getElementById('terms_conditions');
      if (!terms || !terms.checked) errors.push(['Bitte akzeptieren Sie die Allgemeinen Geschäftsbedingungen.', terms]);
    }

    showAlert(stepNo, errors.length ? ('⚠️ ' + errors[0][0]) : '');
    return errors.length === 0;
  }

  form.addEventListener('click', e => {
    const next = e.target.closest('[data-next]');
    const prev = e.target.closest('[data-prev]');
    if (next) {
      e.preventDefault(); clearAlerts();
      if (step === 1) { step = 2; render(step); return; }
      if (step === 2 && validateStep(2)) { step = 3; render(step); return; }
    }
    if (prev) {
      e.preventDefault(); clearAlerts();
      step = Math.max(1, step - 1); render(step);
    }
  });

  // Submit-Blocker in Capture-Phase → greift vor jQuery/Payment
  form.addEventListener('submit', (e) => {
    clearAlerts();
    const ok = validateStep(2) && validateStep(3);
    if (!ok) { e.preventDefault(); return false; }
  }, true);

  function render(s){
    panels.forEach(p => p.classList.toggle('active', p.dataset.step == s));
    if (title) title.textContent = titles[s - 1];
    [1,2,3].forEach(i=>{
      const d = dots(i), l = lines(i);
      if (d) d.classList.toggle('active', i <= s);
      if (l) l.classList.toggle('active', i <  s);
    });
    const c = document.querySelector('#couponBox .collapse');
    if (c && !c.classList.contains('show')) c.classList.add('show');
  }
});
