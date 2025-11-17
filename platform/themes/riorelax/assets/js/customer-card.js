/* customer-card.js
 * Theme-level utilities for customer card UI isolation.
 * Keeps the payment button state untouched when card events fire.
 */
(function (win, $) {
  'use strict';
  if (!win || !win.document) return;

  function lockCheckoutButton() {
    var btn = win.document.querySelector('.payment-checkout-btn');
    if (!btn) return;
    if (btn.dataset.cardLocked === 'true') return;
    btn.dataset.cardLocked = 'true';
  }

  // Native events (falls künftig CustomEvents aus Vanilla kommen)
  win.document.addEventListener('customer-card.applied', lockCheckoutButton, { passive: true });
  win.document.addEventListener('customer-card.removed', lockCheckoutButton, { passive: true });

  // jQuery Events (Plugin feuert aktuell per $(document).trigger)
  if ($ && $.fn && typeof $.fn.on === 'function') {
    $(win.document)
      .off('customer-card.applied.cardLock customer-card.removed.cardLock')
      .on('customer-card.applied.cardLock customer-card.removed.cardLock', lockCheckoutButton);
  }
})(window, window.jQuery);
