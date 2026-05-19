@php
    $openingStart = \Botble\Hotel\Supports\OpeningHours::startLabel();
    $openingEnd = \Botble\Hotel\Supports\OpeningHours::endLabel();
@endphp
<div
    id="room-request-modal"
    class="room-request-modal"
    aria-hidden="true"
    data-popup-url-template="{{ route('public.rooms.popup', ['room' => '__ROOM__']) }}"
    data-submit-url="{{ route('public.send.room-request') }}"
    data-opening-start="{{ $openingStart }}"
    data-opening-end="{{ $openingEnd }}"
    data-opening-violation="{{ trans('plugins/hotel::booking.opening_hours_violation', ['start' => $openingStart, 'end' => $openingEnd]) }}"
>
    <div class="room-request-modal__backdrop" data-room-request-close></div>
    <div class="room-request-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="room-request-title">
        <button type="button" class="room-request-modal__close" aria-label="{{ __('Close') }}" data-room-request-close>&times;</button>

        <div class="room-request-card">
            <img class="room-request-card__image" src="" alt="" data-room-request-image>
            <div>
                <h3 id="room-request-title" class="room-request-card__title" data-room-request-name></h3>
                <div class="room-request-card__features" data-room-request-features></div>
            </div>
        </div>

        <form id="room-request-form" class="room-request-form" novalidate>
            @csrf
            <input type="hidden" name="room_id" id="room-request-room-id">
            <input type="hidden" name="room_name" id="room-request-room-name">

            <div class="room-request-grid">
                <div class="room-request-field">
                    <label for="room-request-name">{{ __('Name') }}*</label>
                    <input id="room-request-name" type="text" name="name" required>
                    <small data-error-for="name"></small>
                </div>
                <div class="room-request-field">
                    <label for="room-request-company">{{ __('Company') }}</label>
                    <input id="room-request-company" type="text" name="company">
                    <small data-error-for="company"></small>
                </div>
                <div class="room-request-field">
                    <label for="room-request-email">{{ __('Email') }}*</label>
                    <input id="room-request-email" type="email" name="email" required>
                    <small data-error-for="email"></small>
                </div>
                <div class="room-request-field">
                    <label for="room-request-phone">{{ __('Phone') }}*</label>
                    <input id="room-request-phone" type="text" name="phone" required>
                    <small data-error-for="phone"></small>
                </div>
                <div class="room-request-field">
                    <label for="room-request-persons">{{ __('Anzahl Personen') }}*</label>
                    <input id="room-request-persons" type="number" name="persons" min="1" required>
                    <small data-error-for="persons"></small>
                </div>
                <div class="room-request-field">
                    <label for="room-request-time-from">{{ __('Zeit von') }}*</label>
                    <input id="room-request-time-from" type="datetime-local" name="time_from" required>
                    <small data-error-for="time_from"></small>
                </div>
                <div class="room-request-field">
                    <label for="room-request-time-to">{{ __('Zeit bis') }}*</label>
                    <input id="room-request-time-to" type="datetime-local" name="time_to" required>
                    <small data-error-for="time_to"></small>
                </div>
            </div>

            <p class="room-request-hint">
                {{ trans('plugins/hotel::booking.opening_hours_hint', ['start' => $openingStart, 'end' => $openingEnd]) }}
            </p>

            <div class="room-request-field">
                <label for="room-request-content">{{ __('Persönliche Nachricht an Inspira') }}*</label>
                <textarea id="room-request-content" name="content" rows="3" required></textarea>
                <small data-error-for="content"></small>
            </div>


            @if (setting('contact_form_show_terms_checkbox', true))
                <div class="room-request-field room-request-field--terms">
                    <label class="room-request-checkbox">
                        <input type="checkbox" name="agree_terms_and_policy" value="1" required>
                        <span>{!! BaseHelper::clean(__('I agree to the :link', ['link' => '<a href="' . BaseHelper::getHomepageUrl() . '/privacy-policy" target="_blank" rel="noopener">' . __('Privacy Policy') . '</a>'])) !!}</span>
                    </label>
                    <small data-error-for="agree_terms_and_policy"></small>
                </div>
            @endif

            <p class="room-request-status" data-room-request-status aria-live="polite"></p>

            <div class="room-request-actions">
                <button type="button" class="btn btn-secondary" data-room-request-close>{{ __('Abbrechen') }}</button>
                <button type="submit" class="btn btn-primary" data-room-request-submit>{{ __('Senden') }}</button>
            </div>
        </form>
    </div>
</div>

<style>
.room-request-modal{position:fixed;inset:0;z-index:9999;display:none}.room-request-modal.is-open{display:block}
.room-request-modal__backdrop{position:absolute;inset:0;background:rgba(0,0,0,.5)}
.room-request-modal__dialog{position:relative;background:#fff;max-width:640px;width:min(92vw,640px);max-height:90vh;margin:4vh auto;padding:14px 14px 12px;border-radius:10px;overflow:auto}
.room-request-modal__close{position:absolute;right:12px;top:8px;border:none;background:transparent;font-size:28px;line-height:1}
.room-request-card{display:grid;grid-template-columns:170px 1fr;gap:10px;margin-bottom:10px}.room-request-card__image{width:100%;aspect-ratio:16/10;object-fit:cover;border-radius:6px;background:#f3f3f3}
.room-request-card__title{margin:0 0 4px;font-size:22px;line-height:1.15}.room-request-card__features{display:flex;gap:6px;flex-wrap:wrap}.room-request-card__features img{width:18px;height:18px}
.room-request-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}
.room-request-field label{display:block;font-size:12px;margin-bottom:2px}.room-request-field input,.room-request-field textarea{width:100%;border:1px solid #d9d9d9;border-radius:6px;padding:7px 9px}
.room-request-field small{display:block;color:#c0392b;min-height:14px;margin-top:1px}.room-request-field--terms{margin-top:4px}.room-request-checkbox{display:flex!important;align-items:flex-start;gap:8px;font-size:13px}.room-request-checkbox input{width:auto;margin-top:3px}.room-request-checkbox a{text-decoration:underline}.room-request-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:8px}.room-request-actions .btn{min-width:130px;padding:8px 14px}
.room-request-status{min-height:20px}.room-request-status.is-success{color:#2d7a2d}.room-request-status.is-error{color:#c0392b}
.room-request-hint{margin:6px 0 0;font-size:12px;color:#5E8E84}
@media (max-width:767px){.room-request-modal__dialog{margin:2vh auto;padding:12px}.room-request-card{grid-template-columns:1fr}.room-request-grid{grid-template-columns:1fr}.room-request-actions .btn{min-width:0;flex:1}}
</style>

<script>
(() => {
  const modal = document.getElementById('room-request-modal');
  if (!modal) return;

  const dialog = modal.querySelector('.room-request-modal__dialog');
  const form = document.getElementById('room-request-form');
  const statusEl = modal.querySelector('[data-room-request-status]');
  const submitBtn = modal.querySelector('[data-room-request-submit]');
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  let lastTrigger = null;

  const focusableSelector = 'a[href],button,textarea,input,select,[tabindex]:not([tabindex="-1"])';

  const resetErrors = () => {
    modal.querySelectorAll('[data-error-for]').forEach(el => el.textContent = '');
    statusEl.textContent = '';
    statusEl.className = 'room-request-status';
  };

  const trapFocus = (e) => {
    if (e.key !== 'Tab') return;
    const focusable = [...dialog.querySelectorAll(focusableSelector)].filter(el => !el.disabled);
    if (!focusable.length) return;
    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    if (e.shiftKey && document.activeElement === first) {
      e.preventDefault();
      last.focus();
    } else if (!e.shiftKey && document.activeElement === last) {
      e.preventDefault();
      first.focus();
    }
  };

  const openModal = async (trigger) => {
    lastTrigger = trigger;
    resetErrors();
    form.reset();

    const roomId = trigger.dataset.roomId;
    const url = modal.dataset.popupUrlTemplate.replace('__ROOM__', roomId);

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    modal.querySelector('[data-room-request-image]').src = '';
    modal.querySelector('[data-room-request-image]').alt = '';
    modal.querySelector('[data-room-request-name]').textContent = '{{ __('Lade Raum...') }}';
    modal.querySelector('[data-room-request-features]').innerHTML = '';
    statusEl.textContent = 'Lade Raumdaten...';

    try {
      const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const json = await response.json().catch(() => ({}));
      const room = json.data || {};

      modal.querySelector('[data-room-request-image]').src = room.image_url || '';
      modal.querySelector('[data-room-request-image]').alt = room.name || '';
      modal.querySelector('[data-room-request-name]').textContent = room.name || '';
      document.getElementById('room-request-room-id').value = room.id || '';
      document.getElementById('room-request-room-name').value = room.name || '';

      const featuresWrap = modal.querySelector('[data-room-request-features]');
      featuresWrap.innerHTML = '';
      (room.features || []).forEach((feature) => {
        const node = document.createElement(feature.icon_url ? 'img' : 'span');
        if (feature.icon_url) {
          node.src = feature.icon_url;
          node.alt = feature.name || '';
          node.title = feature.name || '';
        } else {
          node.textContent = feature.name || '';
        }
        featuresWrap.appendChild(node);
      });

      statusEl.textContent = '';
      form.querySelector('input[name="name"]').focus();
    } catch (_) {
      statusEl.textContent = 'Zimmerdaten konnten nicht geladen werden.';
      statusEl.classList.add('is-error');
    }
  };

  const closeModal = () => {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    if (lastTrigger) lastTrigger.focus();
  };

  document.addEventListener('click', (e) => {
    const trigger = e.target.closest('[data-room-request-trigger]');
    if (trigger) {
      e.preventDefault();
      openModal(trigger);
      return;
    }

    if (e.target.closest('[data-room-request-close]') && modal.classList.contains('is-open')) {
      closeModal();
    }
  });

  document.addEventListener('keydown', (e) => {
    if (!modal.classList.contains('is-open')) return;
    if (e.key === 'Escape') closeModal();
    trapFocus(e);
  });

  const parseHHMM = (value) => {
    const m = (value || '').match(/^(\d{1,2}):(\d{2})$/);
    if (!m) return null;
    const h = parseInt(m[1], 10), mm = parseInt(m[2], 10);
    if (h < 0 || h > 23 || mm < 0 || mm > 59) return null;
    return h * 60 + mm;
  };
  const dateTimeMinutes = (value) => {
    const m = (value || '').match(/^\d{4}-\d{2}-\d{2}T(\d{2}:\d{2})/);
    return m ? parseHHMM(m[1]) : null;
  };

  const validateOpeningHours = () => {
    const startMin = parseHHMM(modal.dataset.openingStart) ?? 600;
    const endMin = parseHHMM(modal.dataset.openingEnd) ?? 1260;
    const message = modal.dataset.openingViolation || 'Außerhalb der Öffnungszeiten.';

    const checks = [
      { field: 'time_from', el: form.querySelector('input[name="time_from"]') },
      { field: 'time_to', el: form.querySelector('input[name="time_to"]') },
    ];

    let hasError = false;
    checks.forEach(({ field, el }) => {
      if (!el) return;
      const minutes = dateTimeMinutes(el.value);
      if (minutes === null) return;
      if (minutes < startMin || minutes > endMin) {
        const errEl = modal.querySelector(`[data-error-for="${field}"]`);
        if (errEl) errEl.textContent = message;
        hasError = true;
      }
    });

    if (hasError) {
      statusEl.textContent = message;
      statusEl.classList.add('is-error');
    }
    return !hasError;
  };

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    resetErrors();

    if (!validateOpeningHours()) return;

    submitBtn.disabled = true;
    submitBtn.textContent = '{{ __('Senden...') }}';

    const formData = new FormData(form);

    try {
      const response = await fetch(modal.dataset.submitUrl, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        body: formData,
      });

      const json = await response.json().catch(() => ({}));

      if (!response.ok || json.error) {
        if (response.status === 429) {
          statusEl.textContent = 'Zu viele Anfragen in kurzer Zeit. Bitte warte kurz und versuche es erneut.';
          statusEl.classList.add('is-error');
          return;
        }

        if (json.errors) {
          Object.entries(json.errors).forEach(([field, messages]) => {
            const errorEl = modal.querySelector(`[data-error-for="${field}"]`);
            if (errorEl) errorEl.textContent = Array.isArray(messages) ? messages[0] : messages;
          });
        }
        statusEl.textContent = json.message || 'Bitte prüfe deine Eingaben.';
        statusEl.classList.add('is-error');
        return;
      }

      statusEl.textContent = json.message || 'Anfrage erfolgreich gesendet.';
      statusEl.classList.add('is-success');
      form.reset();
    } catch (_) {
      statusEl.textContent = 'Die Anfrage konnte nicht gesendet werden.';
      statusEl.classList.add('is-error');
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = '{{ __('Senden') }}';
    }
  });
})();
</script>
