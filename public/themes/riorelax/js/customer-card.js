/* customer-card.js
 * Theme-level utilities for customer card UI isolation.
 * Keeps the payment button state untouched when card events fire.
 */
(function (win) {
  'use strict';
  if (!win || !win.document) return;

  function lockCheckoutButton() {
    var btn = win.document.querySelector('.payment-checkout-btn');
    if (!btn) return;
    if (btn.dataset.cardLocked === 'true') return;
    btn.dataset.cardLocked = 'true';
  }

  win.document.addEventListener('customer-card.applied', lockCheckoutButton, { passive: true });
  win.document.addEventListener('customer-card.removed', lockCheckoutButton, { passive: true });
})(window);
