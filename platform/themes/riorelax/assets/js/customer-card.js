/* customer-card.js
 * Theme-level utilities for customer card UI isolation.
 * Keeps the payment button state untouched when card events fire.
 */
(function (win, $) {
  'use strict';
  if (!win || !win.document) return;

  var removeUrl =
    (win.customerCard && win.customerCard.routes && win.customerCard.routes.remove) || null;

  function lockCheckoutButton() {
    var btn = win.document.querySelector('.payment-checkout-btn');
    if (!btn) return;
    if (btn.dataset.cardLocked === 'true') return;
    btn.dataset.cardLocked = 'true';
  }

  if ($ && $.fn && typeof $.fn.on === 'function') {
    var $document = $(win.document);
    var $removeButton = $('[data-bb-customer-card="remove"]');

    removeUrl = removeUrl || $removeButton.data('url') || null;

    $removeButton.attr('data-card-remove', 'true');

    $document
      .off('customer-card.applied.cardLock customer-card.removed.cardLock')
      .on('customer-card.applied.cardLock customer-card.removed.cardLock', lockCheckoutButton);
  }

  // Native events (falls künftig CustomEvents aus Vanilla kommen)
  win.document.addEventListener('customer-card.applied', lockCheckoutButton, { passive: true });
  win.document.addEventListener('customer-card.removed', lockCheckoutButton, { passive: true });
})(window, window.jQuery);
