@if (is_plugin_active('payment'))
  <link rel="stylesheet" href="{{ asset('vendor/core/plugins/payment/css/payment.css') }}?v=1.0.6">
  @php
    Theme::asset()->container('header')->usePath()->add('jquery', 'plugins/jquery.min.js');
    Theme::asset()->container('header')->add('payment-js', 'vendor/core/plugins/payment/js/payment.js');
  @endphp
  {!! apply_filters(PAYMENT_FILTER_HEADER_ASSETS, null) !!}
@endif

@php
  Theme::set('pageTitle', '');
  Theme::asset()->container('footer')->usePath()->add('checkout-js', 'js/course-checkout.js');
  $startLabel24 = BaseHelper::formatDate($session->start_date, 'd.m.Y H:i');
  $endLabel24   = $session->end_date ? BaseHelper::formatDate($session->end_date, 'd.m.Y H:i') : null;
@endphp

<script>
(function(){
  const form   = document.getElementById('bookingForm');
  const panels = [...document.querySelectorAll('.step-panel')];
  const title  = document.getElementById('stepTitle');
  const dots   = i => document.querySelector('[data-step-dot="'+i+'"]');
  const lines  = i => document.querySelector('[data-step-line="'+i+'"]');
  const titles = ['Allgemeine Informationen','Ihre Angaben','Zahlung & Abschluss'];

  let step = {{ $customer->id ? 2 : 1 }};
  render(step);

  const fieldIds = {
    first: 'txt-first_name',
    last:  'txt-last_name',
    email: 'txt-email',
    phone: 'txt-phone'
  };

  function val(id){ return document.getElementById(id)?.value.trim() || ''; }
  function markInvalid(el, invalid){ if(el) el.classList.toggle('is-invalid', !!invalid); }
  function emailOk(s){ return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(s); }
  function phoneOk(s){ return /^[0-9+\s\-()]+$/.test(s); }

  function showAlert(stepNo, msg){
    const box = document.getElementById(stepNo === 2 ? 'formAlertStep2' : 'formAlertStep3');
    if(!box) return;
    box.textContent = msg;
    box.style.display = msg ? 'block' : 'none';
    if(msg) box.scrollIntoView({behavior:'smooth', block:'center'});
  }

  function clearAlerts(){
    ['formAlertStep2','formAlertStep3'].forEach(id=>{
      const el=document.getElementById(id);
      if(el){ el.style.display='none'; el.textContent=''; }
    });
  }

  function validateStep(stepNo){
    const fFirst = document.getElementById(fieldIds.first);
    const fLast  = document.getElementById(fieldIds.last);
    const fEmail = document.getElementById(fieldIds.email);
    const fPhone = document.getElementById(fieldIds.phone);

    const vFirst = val(fieldIds.first);
    const vLast  = val(fieldIds.last);
    const vEmail = val(fieldIds.email);
    const vPhone = val(fieldIds.phone);

    let errors = [];

    // Pflichtfelder
    if(!vFirst){ errors.push('Vorname ist erforderlich.'); markInvalid(fFirst,true); } else markInvalid(fFirst,false);
    if(!vLast){ errors.push('Nachname ist erforderlich.'); markInvalid(fLast,true); } else markInvalid(fLast,false);

    if(!vEmail){
      errors.push('E-Mail ist erforderlich.'); markInvalid(fEmail,true);
    } else if(!emailOk(vEmail)){
      errors.push('Bitte geben Sie eine gültige E-Mail-Adresse ein.'); markInvalid(fEmail,true);
    } else markInvalid(fEmail,false);

    if(!vPhone){
      errors.push('Telefon ist erforderlich.'); markInvalid(fPhone,true);
    } else if(!phoneOk(vPhone)){
      errors.push('Bitte geben Sie eine gültige Telefonnummer ein.'); markInvalid(fPhone,true);
    } else if(vPhone.replace(/\D/g,'').length < 8){
      errors.push('Die Telefonnummer muss mindestens 8 Ziffern enthalten.'); markInvalid(fPhone,true);
    } else markInvalid(fPhone,false);

    // AGB Pflicht (nur Step 3)
    if(stepNo === 3){
      const terms = document.getElementById('terms_conditions');
      if(!terms || !terms.checked){
        errors.push('Bitte akzeptieren Sie die Allgemeinen Geschäftsbedingungen, um fortzufahren.');
      }
    }

    showAlert(stepNo, errors.length ? ('⚠️ ' + errors[0]) : '');
    return errors.length === 0;
  }

  // Live Validation für Step 2
  form.addEventListener('input', ()=>{
    if(step===2){
      const nextBtn = form.querySelector('[data-step="2"] [data-next]');
      if(nextBtn){
        const ok = validateStep(2);
        nextBtn.disabled = !ok;
        if(ok) showAlert(2,'');
      }
    }
  });

  // Navigation Next / Prev + Final Submit
  form.addEventListener('click', e=>{
    const next   = e.target.closest('[data-next]');
    const prev   = e.target.closest('[data-prev]');
    const finish = e.target.closest('.payment-checkout-btn'); // Abschließen

    if(next){
      e.preventDefault();
      clearAlerts();

      if(step===1){ step=2; render(step); return; }

      if(step===2 && !validateStep(2)) return;

      step = Math.min(step+1,3);
      render(step);

      // ⚠️ Direkt nach Wechsel zu Step 3 → AGB prüfen
      if(step === 3){
        const terms = document.getElementById('terms_conditions');
        if(!terms || !terms.checked){
          showAlert(3,'⚠️ Bitte akzeptieren Sie die Allgemeinen Geschäftsbedingungen, um fortzufahren.');
        } else {
          showAlert(3,'');
        }
      }
    }

    if(prev){
      e.preventDefault();
      clearAlerts();
      step = Math.max(step-1,1);
      render(step);
    }

    // Abschließen manuell prüfen
    if(finish){
      e.preventDefault();
      clearAlerts();
      const ok2 = validateStep(2);
      const ok3 = validateStep(3);
      if(!(ok2 && ok3)){
        if(!ok2 && step!==2){ step=2; render(step); }
        else if(!ok3 && step!==3){ step=3; render(step); }
        return;
      }
      form.submit();
    }
  });

  // AGB Checkbox aktiviert/deaktiviert live den Abschließen-Button
  const termsBox = document.getElementById('terms_conditions');
  if(termsBox){
    const submitBtn = form.querySelector('.payment-checkout-btn');
    const toggleState = () => {
      submitBtn.disabled = !termsBox.checked;
      if(!termsBox.checked){
        showAlert(3,'⚠️ Bitte akzeptieren Sie die Allgemeinen Geschäftsbedingungen, um fortzufahren.');
      } else {
        showAlert(3,'');
      }
    };
    toggleState();
    termsBox.addEventListener('change', toggleState);
  }

  // Step-Render
  function render(s){
    panels.forEach(p=>p.classList.toggle('active', p.dataset.step==s));
    title.textContent = titles[s-1];
    [1,2,3].forEach(i=>{
      const d=dots(i), l=lines(i);
      if(d)d.classList.toggle('active',i<=s);
      if(l)l.classList.toggle('active',i<s);
    });
    // Coupon immer offen
    const c=document.querySelector('#couponBox .collapse');
    if(c&&!c.classList.contains('show'))c.classList.add('show');
  }
})();
</script>

