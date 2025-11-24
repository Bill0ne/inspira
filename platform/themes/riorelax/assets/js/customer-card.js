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

  function getCourseId() {
    var fromInput = win.document.querySelector('input[name="course_id"]');
    if (fromInput && fromInput.value) return fromInput.value;

    var select = win.document.querySelector('#customer_card_select');
    if (select && select.dataset.course) return select.dataset.course;

    return null;
  }

  function handleRemoveSuccess(response) {
    if (typeof win.handleCardRemoveResponse === 'function') {
      win.handleCardRemoveResponse(response);
      return;
    }

    win.CheckoutState = response;

    if (typeof win.renderCheckoutUI === 'function') {
      win.renderCheckoutUI();
    }

    $(win.document).trigger('customer-card.removed', { response: response });
  }

  function handleRemoveError(error) {
    if (typeof win.handleRequestError === 'function') {
      return win.handleRequestError(error);
    }

    if (win.console && typeof win.console.error === 'function') {
      console.error(error);
    }
  }

  if ($ && $.fn && typeof $.fn.on === 'function') {
    var $document = $(win.document);
    var $removeButton = $('[data-bb-customer-card="remove"]');

    removeUrl = removeUrl || $removeButton.data('url') || null;

    $removeButton.attr('data-card-remove', 'true');
    $removeButton.off('click');

    var hasCommerceHandler = typeof win.handleCardRemoveResponse === 'function';

    if (!hasCommerceHandler) {
      $document.on('click', '[data-bb-customer-card="remove"]', function (e) {
        e.preventDefault();

        var $trigger = $(this);
        removeUrl = removeUrl || $trigger.data('url') || null;

        if (!removeUrl) return;

        var payload = {};
        var courseId = getCourseId();
        if (courseId) {
          payload.course_id = courseId;
        }

        $.post(removeUrl, payload).done(handleRemoveSuccess).fail(handleRemoveError);
      });
    }

    $document
      .off('customer-card.applied.cardLock customer-card.removed.cardLock')
      .on('customer-card.applied.cardLock customer-card.removed.cardLock', lockCheckoutButton);
  }

  // Native events (falls künftig CustomEvents aus Vanilla kommen)
  win.document.addEventListener('customer-card.applied', lockCheckoutButton, { passive: true });
  win.document.addEventListener('customer-card.removed', lockCheckoutButton, { passive: true });
})(window, window.jQuery);
