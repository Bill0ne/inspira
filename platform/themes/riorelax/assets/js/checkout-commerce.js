/* checkout-commerce.js
 * Gemeinsames Commerce: Coupon apply/remove + Payment-Liste reload
 * Robust gegen 0-Werte (nutzt 'in' Checks), sichert CSRF und Fehlerfälle ab.
 */
(function (win, $) {
  'use strict';
  if (!$) return;

  function getContextRoot(context) {
    if (!context) return null;
    return document.querySelector('[data-checkout-context="' + context + '"]');
  }

  function hasContext(context) {
    var root = getContextRoot(context) || document;
    if (context === 'course') {
      return !!root.querySelector('input[name="course_id"]') || !!document.querySelector('input[name="course_id"]');
    }
    if (context === 'hotel') {
      return !!root.querySelector('input[name="room_id"]') || !!document.querySelector('input[name="room_id"]');
    }
    return false;
  }

  function ensureCourseContext(action) {
    if (hasContext('course') || hasContext('hotel')) {
      return true;
    }

    var fallbackMessage = 'Checkout-Kontext konnte nicht ermittelt werden.';
    if (action) {
      fallbackMessage += ' (' + action + ')';
    }

    callTheme('showError', fallbackMessage, function (msg) {
      if (win.alert) alert(msg);
    });

    return false;
  }

  function getActiveContext(fallback) {
    if (fallback && hasContext(fallback)) return fallback;
    if (hasContext('course')) return 'course';
    if (hasContext('hotel')) return 'hotel';
    return null;
  }

  function createContextObject(type, fallbackRoot) {
    if (!type) return null;
    var root = getContextRoot(type) || fallbackRoot;
    if (!root) return null;
    var selector = type === 'hotel' ? '#hotelCouponBox' : '#courseCouponBox';
    var target = root.querySelector(selector);
    if (!target) {
      target = root.querySelector('.coupon-wrapper');
    }
    return {
      type: type,
      root: root,
      $root: $(root),
      $box: $(target || [])
    };
  }

  function resolveContext(element) {
    var el = element && element.nodeType ? element : null;
    while (el) {
      if (el.nodeType !== 1) {
        el = el.parentElement || el.parentNode;
        continue;
      }
      if (el.hasAttribute('data-checkout-context')) {
        var type = el.getAttribute('data-checkout-context');
        if (type && hasContext(type)) {
          return createContextObject(type, el);
        }
      }
      el = el.parentElement;
    }
    return null;
  }

  function ensureContextRoot(context) {
    var ctx = getActiveContext(context);
    return ctx ? getContextRoot(ctx) : null;
  }

  function getCouponContainer(context) {
    var root = ensureContextRoot(context);
    if (!root) return $();
    var id = context === 'hotel' ? '#hotelCouponBox' : '#courseCouponBox';
    var target = root.querySelector(id);
    if (!target) {
      target = root.querySelector('.coupon-wrapper');
    }
    return $(target || []);
  }

  var COUPON_STATE_ATTR = 'data-coupon-open';
  var COUPON_OPEN_CLASS = 'coupon-form-open';

  var activeCouponRequest = null;

  function getStoredCouponState(ctx) {
    if (!ctx || !ctx.root) return null;
    var attr = ctx.root.getAttribute(COUPON_STATE_ATTR);
    if (attr === 'true') return true;
    if (attr === 'false') return false;
    return null;
  }

  function setStoredCouponState(ctx, isOpen) {
    if (!ctx || !ctx.root) return;
    ctx.root.setAttribute(COUPON_STATE_ATTR, isOpen ? 'true' : 'false');
  }

  function applyCouponFormState(ctx, isOpen, options) {
    if (!ctx || !ctx.$box || !ctx.$box.length) return;
    var $form = ctx.$box.find('.coupon-form');
    if (!$form.length) return;

    var targetState = typeof isOpen === 'boolean' ? isOpen : getStoredCouponState(ctx);
    if (typeof targetState !== 'boolean') {
      targetState = $form.is(':visible');
    }

    var animate = options && options.animate;
    if (animate) {
      $form.stop(true, true);
      if (targetState) {
        $form.slideDown('fast');
      } else {
        $form.slideUp('fast');
      }
    } else {
      $form.toggle(targetState);
    }

    ctx.$box.toggleClass(COUPON_OPEN_CLASS, !!targetState);
    setStoredCouponState(ctx, targetState);
    return targetState;
  }

  function restoreCouponFormState(context) {
    var ctxType = getActiveContext(context);
    if (!ctxType) return;
    var ctx = createContextObject(ctxType);
    if (!ctx) return;
    applyCouponFormState(ctx);
  }

  function getCsrf() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
  }

  function callTheme(method, value, fallback) {
    var theme = win.RiorelaxTheme || {};
    var fn = theme && typeof theme[method] === 'function' ? theme[method] : null;
    if (fn) {
      fn.call(theme, value);
    } else if (typeof fallback === 'function') {
      fallback(value);
    }
  }

  win.CheckoutState = win.CheckoutState || { card: null, coupon: null, totals: {} };
  var removeUrl =
    (win.customerCard && win.customerCard.routes && win.customerCard.routes.remove) || null;

  function extractCheckoutState(payload) {
    if (!payload || typeof payload !== 'object') return null;
    if (payload.data && typeof payload.data === 'object' && !Array.isArray(payload.data)) {
      return payload.data;
    }
    return payload;
  }

  function handleRequestError(error) {
    if (win.console && console.error) {
      console.error('[Checkout] Request error:', error);
    }

    if (win.Botble && typeof win.Botble.showError === 'function') {
      win.Botble.showError('Es ist ein Fehler beim Aktualisieren des Checkouts aufgetreten.');
    }
  }

  function syncCustomerCardPayment(context) {
    var ctxType = getActiveContext(context);
    var root = getContextRoot(ctxType) || document;
    var $root = $(root);
    var $form = $root.find('.payment-checkout-form');

    if (!$form.length) return;

    var $amountInput = $root.find('input[name="amount"]');
    var amountRaw = parseFloat($amountInput.val());
    if (!isFinite(amountRaw)) amountRaw = 0;

    var cardId = ($root.find('[data-customer-card-input]').val() || '').toString().trim();
    var hasCardPayment = !!cardId && amountRaw <= 0;

    var $paymentOptions = $form.find('.list_payment_method input[name="payment_method"]');
    var $hidden = $form.find('input[name="payment_method"][data-customer-card-method]');

    if (hasCardPayment) {
      $paymentOptions.prop('checked', false).prop('disabled', true);

      if (!$hidden.length) {
        $hidden = $('<input>', {
          type: 'hidden',
          name: 'payment_method',
          'data-customer-card-method': '1'
        }).appendTo($form);
      }

      $hidden.val('customer_card');
    } else {
      $paymentOptions.prop('disabled', false);

      if ($hidden.length) {
        $hidden.remove();
      }
    }
  }

  function renderCheckoutUI(context) {
    var state = win.CheckoutState || {};
    var totals = state.totals || {};
    var ctxType = getActiveContext(context);
    var root = getContextRoot(ctxType) || document;
    var $root = $(root);

    var amountText = totals.sub_total_display ?? state.sub_total;
    if (typeof amountText !== 'undefined') {
      $root.find('.amount-text').text(amountText);
    }

    var discountText = totals.discount_display ?? state.discount_amount;
    if (typeof discountText !== 'undefined') {
      $root.find('.discount-text').text(discountText);
    }

    var taxText = totals.tax_display ?? state.tax_amount;
    if (typeof taxText !== 'undefined') {
      $root.find('.tax-text').text(taxText);
    }

    var totalText = totals.total_display ?? state.total_amount;
    if (typeof totalText !== 'undefined') {
      $root.find('.total-amount-text').text(totalText);
    }

    var amountRaw = totals.total_raw ?? state.amount_raw;
    var $amountInput = $root.find('input[name="amount"]');
    if ($amountInput.length && typeof amountRaw !== 'undefined') {
      $amountInput.val(amountRaw);

      if ('total_before_card_raw' in totals) {
        $amountInput.data('original-total', totals.total_before_card_raw);
      }
      if ('card_discount_raw' in totals) {
        $amountInput.data('active-discount', totals.card_discount_raw);
      }
      if ('minimum_fee_raw' in totals) {
        $amountInput.data('minimum-fee', totals.minimum_fee_raw);
      }
      if ('minimum_threshold' in totals) {
        $amountInput.data('minimum-threshold', totals.minimum_threshold);
      }
    }

    syncCustomerCardPayment(context);

    var $cardDiscountRow = $root.find('.card-discount-row');
    var $cardDiscountText = $root.find('.card-discount-text');
    var $cardUnitPriceRow = $root.find('.card-unit-price-row');
    var $cardUnitPriceText = $root.find('.card-unit-price-text');
    if ($cardDiscountRow.length) {
      var cardDiscountRaw = totals.card_discount_raw ?? 0;
      $cardDiscountRow.toggleClass('d-none', !(cardDiscountRaw > 0));
      if (totals.card_discount_display) {
        $cardDiscountText.text(totals.card_discount_display);
      }
    }

    if ($cardUnitPriceRow.length) {
      var cardUnitPriceRaw = totals.card_unit_price_raw ?? 0;
      $cardUnitPriceRow.toggleClass('d-none', !(cardUnitPriceRaw > 0));

      if (totals.card_unit_price_display) {
        $cardUnitPriceText.text(totals.card_unit_price_display);
      }
    }

    var $minimumFeeRow = $root.find('.minimum-fee-row');
    if ($minimumFeeRow.length && 'minimum_fee_raw' in totals) {
      $minimumFeeRow.toggleClass('d-none', !((totals.minimum_fee_raw || 0) > 0));
      if (totals.minimum_fee_display) {
        $minimumFeeRow.find('.minimum-fee-text').text(totals.minimum_fee_display);
      }
    }

    var $cardSelect = $('#customer_card_select');
    var $removeButton = $('[data-bb-customer-card="remove"]');
    var $infoBox = $('[data-bb-customer-card="info"]');
    var $infoDiscount = $infoBox.find('[data-bb-customer-card="discount"]');
    var $cardInput = $('[data-customer-card-input]');
    var $cardIdInput = $root.find('input[name="customer_card_id"]');
    var $cardUnitsInput = $root.find('input[name="customer_card_units_used"]');
    var $cardCoverageInput = $root.find('input[name="customer_card_coverage_type"]');
    var $cardDiscountInput = $root.find('input[name="customer_card_discount"]');
    var $cardSection = $root.find('[data-bb-customer-card-section]');
    var cardState = state.card;
    var hasActiveCard = !!(cardState && cardState.id);

    if (hasActiveCard) {
      if ($cardSelect.length) $cardSelect.val(String(cardState.id));
      if ($cardInput.length) $cardInput.val(cardState.id);
      if ($cardIdInput.length) $cardIdInput.val(cardState.id);
      if ($cardUnitsInput.length) $cardUnitsInput.val(cardState.units_used || 1);
      if ($cardCoverageInput.length) $cardCoverageInput.val(cardState.coverage_type || 'none');
      if ($cardDiscountInput.length) $cardDiscountInput.val(cardState.discount || 0);
      if ($removeButton.length) {
        $removeButton.removeClass('d-none').prop('disabled', false);
      }
      if ($infoBox.length) {
        $infoBox.toggleClass('d-none', !((totals.card_discount_raw || 0) > 0));
        var cardText = totals.card_discount_display_plain || cardState.discount_display;
        if (cardText) {
          $infoDiscount.text(cardText);
        }
      }
    } else {
      if ($cardSelect.length) {
        $cardSelect.val('');
        var $firstOption = $cardSelect.find('option').first();
        if ($firstOption.length) {
          $firstOption.prop('selected', true);
          $firstOption.text('Keine Karte auswählen');
        }
      }
      if ($cardInput.length) $cardInput.val('');
      if ($cardIdInput.length) $cardIdInput.val('');
      if ($cardUnitsInput.length) $cardUnitsInput.val('');
      if ($cardCoverageInput.length) $cardCoverageInput.val('');
      if ($cardDiscountInput.length) $cardDiscountInput.val('');
      if ($removeButton.length) {
        $removeButton.addClass('d-none').prop('disabled', true);
      }
      if ($infoBox.length) {
        $infoBox.addClass('d-none');
        $infoDiscount.text('');
      }
      if ($cardDiscountRow.length) {
        $cardDiscountRow.addClass('d-none');
        $cardDiscountText.text('-');
      }
      if ($cardUnitPriceRow.length) {
        $cardUnitPriceRow.addClass('d-none');
        $cardUnitPriceText.text('-');
      }
      $totalInput.data('active-discount', 0).data('minimum-fee', 0);
    }

    var couponState = state.coupon || {};
    var couponCode = couponState.code || state.coupon_code || '';
    var hasActiveCoupon = !!couponCode;
    if (couponCode) {
      $root.find('input[name="coupon_code"]').val(couponCode);
      $root.find('input[name="coupon_hidden"]').val(couponCode);
    }

    var $couponContainer = getCouponContainer(ctxType);
    if ($couponContainer.length) {
      $couponContainer.toggleClass('d-none', hasActiveCard);
    }

    if ($cardSection.length) {
      $cardSection.toggleClass('d-none', hasActiveCoupon);
    }

    if (state.views && state.views.coupon_box) {
      refreshCouponBox(state.views.coupon_box, ctxType);
    }

    return state;
  }

  var renderTimer = null;

  function scheduleRender(context) {
    if (renderTimer) {
      clearTimeout(renderTimer);
    }

    renderTimer = setTimeout(function () {
      renderTimer = null;
      renderCheckoutUI(context);
    }, 120);
  }

  function setCheckoutState(payload, context) {
    var nextState = extractCheckoutState(payload);
    if (!nextState) return win.CheckoutState;

    var mergedState = Object.assign({}, win.CheckoutState || {});

    if ('totals' in nextState) mergedState.totals = nextState.totals || {};
    if ('card' in nextState) mergedState.card = nextState.card || null;
    if ('coupon' in nextState) mergedState.coupon = nextState.coupon || null;
    if ('views' in nextState) mergedState.views = nextState.views;

    ['sub_total', 'discount_amount', 'tax_amount', 'total_amount', 'amount_raw', 'coupon_code']
      .forEach(function (key) {
        if (key in nextState) mergedState[key] = nextState[key];
      });

    win.CheckoutState = mergedState;
    scheduleRender(context);

    return mergedState;
  }

  function updateTotals(data, context) {
    setCheckoutState(data, context);
  }

  function refreshPaymentMethods(context) {
    return reloadPaymentList(context);
  }

  function handleCardApplyResponse(response) {
    if (!response || response.success === false || response.error) {
      handleRequestError(response);
      return;
    }

    var nextState = setCheckoutState(response.data || response);
    if (!nextState) {
      handleRequestError(response);
      return;
    }

    refreshPaymentMethods();

    $(document).trigger('customer-card.applied', [nextState]);
  }

  function handleCardRemoveResponse(response) {
    if (!response || response.success === false || response.error) {
      handleRequestError(response);
      return;
    }

    var nextState = setCheckoutState(response.data || response);
    if (!nextState) {
      handleRequestError(response);
      return;
    }

    refreshPaymentMethods();

    $(document).trigger('customer-card.removed', [nextState]);
  }

  win.handleCardRemoveResponse = handleCardRemoveResponse;

  function getSharedPayload(context) {
    var payload = {};
    var ctx = context || getActiveContext();

    if (ctx === 'course') {
      payload.course_checkout = true;
    }

    if (context === 'course' || !context) {
      var courseRoot = getContextRoot('course') || document;
      var courseInput = courseRoot.querySelector('input[name="course_id"]') || document.querySelector('input[name="course_id"]');
      if (courseInput && courseInput.value) {
        payload.course_id = courseInput.value;
      }
    }
    if (context === 'hotel' || !context) {
      var roomRoot = getContextRoot('hotel') || document;
      var roomInput = roomRoot.querySelector('input[name="room_id"]') || document.querySelector('input[name="room_id"]');
      if (roomInput && roomInput.value) {
        payload.room_id = roomInput.value;
      }
    }

    return payload;
  }

  function refreshCouponBox(html, context) {
    var ctx = getActiveContext(context);
    if (!ctx) return;
    var $container = getCouponContainer(ctx);
    if (!$container.length) return;
    var ctxInfo = createContextObject(ctx);
    if (ctxInfo) {
      ctxInfo.$box = $container;
    }

    var previousValues = {
      codeInput: ctxInfo && ctxInfo.$box ? ctxInfo.$box.find('input[name=coupon_code]').val() : '',
      hiddenInput: ctxInfo && ctxInfo.$box ? ctxInfo.$box.find('input[name=coupon_hidden]').val() : '',
    };

    if (typeof html === 'string') {
      $container.html(html);
      if (ctxInfo) {
        if (previousValues.hiddenInput && !$container.find('input[name=coupon_hidden]').val()) {
          $container.find('input[name=coupon_hidden]').val(previousValues.hiddenInput);
        }

        if (previousValues.codeInput && !$container.find('input[name=coupon_code]').val()) {
          $container.find('input[name=coupon_code]').val(previousValues.codeInput);
        }

        var hasApplied = $container.find('.coupon-feedback').length > 0;
        var storedState = getStoredCouponState(ctxInfo);
        if (hasApplied && typeof storedState !== 'boolean') {
          applyCouponFormState(ctxInfo, true);
        } else {
          applyCouponFormState(ctxInfo);
        }
      }
      return;
    }

    var $target = $container.find('.coupon-box');
    if (!$target.length) {
      $target = $container;
    }

    var refreshUrl = $target.data('refresh-url');
    if (!refreshUrl) return;

    $.ajax({
      url: refreshUrl,
      type: 'GET',
    })
      .done(function (res) {
        if (res && res.data) {
          refreshCouponBox(res.data, ctx);
        }
      })
      .fail(function (err) {
        callTheme('handleError', err, function () {
          if (win.console && console.error) console.error(err);
        });
      });
  }

  function toggleLoading($el, isLoading) {
    if (!$el || !$el.length) return;

    if (typeof $el.prop === 'function') {
      $el.prop('disabled', !!isLoading);
    }

    $el.toggleClass('button-loading', !!isLoading);
  }

  function reloadPaymentList(context) {
    var ctx = getActiveContext(context);
    if (!ctx) return $.Deferred().resolve();
    var root = getContextRoot(ctx) || document;
    var $list = $(root).find('.payment-checkout-form .list_payment_method');
    if (!$list.length) return $.Deferred().resolve();

    var selected = $list.find('input[name="payment_method"]:checked').val();
    var dfd = $.Deferred();

    $list.load(window.location.href + ' .payment-checkout-form .list_payment_method > *', function (resp, status) {
      if (status === 'error') return dfd.reject();
      if (selected) {
        $list.find('input[name="payment_method"][value="' + selected + '"]').prop('checked', true).trigger('change');
      }
      syncCustomerCardPayment(context);
      dfd.resolve();
    });

    return dfd.promise();
  }

  // Expose für andere Module (Hotel-Recalc ruft das auf)
  var checkoutApi = {
    setState: setCheckoutState,
    renderCheckoutUI: renderCheckoutUI,
    updateTotals: updateTotals,
    reloadPaymentList: reloadPaymentList,
    restoreCouponFormState: restoreCouponFormState
  };

  Object.defineProperties(checkoutApi, {
    isCourseCheckout: {
      get: function () {
        return hasContext('course');
      },
    },
    isHotelCheckout: {
      get: function () {
        return hasContext('hotel');
      },
    },
  });

  win.CheckoutCommerce = checkoutApi;

  var $document = $(document);

  // Sicherstellen, dass wir Events nur einmal binden
  $document.off('click', '.toggle-coupon-form');
  $document.off('click', '.apply-coupon-code');
  $document.off('click', '.remove-coupon-code');
  $document.off('click', '[data-card-remove]');
  $('[data-bb-customer-card="apply"], [data-bb-customer-card="remove"]').off('click');

  var applyUrl = (win.customerCard && win.customerCard.routes && win.customerCard.routes.apply) || null;
  var cardRequestInFlight = false;

  $document.on('click', '[data-bb-customer-card="apply"]', function (e) {
    e.preventDefault();
    var $btn = $(this);
    if (cardRequestInFlight) return;
    cardRequestInFlight = true;
    $btn.prop('disabled', true);

    var cardId = $('#customer_card_select').val();
    applyUrl = applyUrl || $btn.data('url');

    if (!applyUrl || !cardId) {
      cardRequestInFlight = false;
      $btn.prop('disabled', false);
      handleRequestError({ message: 'Kundenkarte oder URL nicht vorhanden.' });
      return;
    }

    if (!ensureCourseContext('Kundenkarte anwenden')) {
      cardRequestInFlight = false;
      $btn.prop('disabled', false);
      return;
    }

    $.ajax({
      url: applyUrl,
      type: 'POST',
      headers: { 'X-CSRF-TOKEN': getCsrf() },
      data: $.extend({ card_id: cardId }, getSharedPayload(getActiveContext())),
    })
      .done(handleCardApplyResponse)
      .fail(handleRequestError)
      .always(function () {
        cardRequestInFlight = false;
        $btn.prop('disabled', false);
      });
  });

  $document.on('click', '[data-card-remove]', function (e) {
    e.preventDefault();
    var $trigger = $(this);
    if (cardRequestInFlight) return;
    cardRequestInFlight = true;
    $trigger.prop('disabled', true);

    removeUrl = removeUrl || $trigger.data('url');
    if (!removeUrl) {
      cardRequestInFlight = false;
      $trigger.prop('disabled', false);
      return;
    }

    $.post(removeUrl, getSharedPayload(getActiveContext()))
      .done(handleCardRemoveResponse)
      .fail(handleRequestError)
      .always(function () {
        cardRequestInFlight = false;
        $trigger.prop('disabled', false);
      });
  });

  $document
    .on('click', '.toggle-coupon-form', function (e) {
      var ctx = resolveContext(e.currentTarget);
      if (!ctx) return;
      if (!hasContext(ctx.type)) return;
      var $form = ctx.$box.find('.coupon-form');
      if (!$form.length) return;
      var willOpen = !$form.is(':visible');
      applyCouponFormState(ctx, willOpen, { animate: true });
    })
    .on('click', '.apply-coupon-code', function (e) {
      e.preventDefault();

      var $btn = $(e.currentTarget);
      var ctx = resolveContext(e.currentTarget);
      if (!ctx) return;
      if (!hasContext(ctx.type)) return;
      var url  = $btn.data('url');
      var $codeInput = ctx.$box.find('input[name=coupon_code]');
      var code = ($codeInput.val() || '').trim();

      if (!url) return;
      if (!code.length) {
        callTheme('showError', 'Bitte Gutscheincode eingeben.', function (msg) {
          if (win.alert) alert(msg);
        });
        return;
      }

      if (!ensureCourseContext('Coupon anwenden')) return;

      if (activeCouponRequest) {
        return;
      }

      activeCouponRequest = $.ajax({
        url: url,
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': getCsrf() },
        data: $.extend({ coupon_code: code }, getSharedPayload(ctx.type)),
        beforeSend: function () { toggleLoading($btn, true); }
      })
      .done(function (res) {
        var error   = res && res.error;
        var message = res && res.message;
        var data    = res && res.data;

        if (error) {
          callTheme('showError', message || 'Fehler beim Anwenden des Gutscheins.', function (msg) {
            if (win.alert) alert(msg);
          });
          return;
        }

        callTheme('showSuccess', message || 'Gutschein angewendet.');
        var state = setCheckoutState(data, ctx.type);
        if (state && state.coupon && state.coupon.code) {
          setStoredCouponState(ctx, true);
        }
        reloadPaymentList(ctx.type);

        // Event für andere Module (z.B. course-checkout.js)
        $(document).trigger('coupon.applied', {
          context: ctx.type,
          response: res,
        });
      })
      .fail(handleRequestError)
      .always(function () {
        toggleLoading($btn, false);
        activeCouponRequest = null;
      });
    })
    .on('click', '.remove-coupon-code', function (e) {
      e.preventDefault();

      var $btn = $(e.currentTarget);
      var ctx = resolveContext(e.currentTarget);
      if (!ctx) return;
      if (!hasContext(ctx.type)) return;
      var url  = $btn.data('url');
      if (!url) return;

      if (!ensureCourseContext('Coupon entfernen')) return;

      if (activeCouponRequest) {
        return;
      }

      activeCouponRequest = $.ajax({
        url: url,
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': getCsrf() },
        data: getSharedPayload(ctx.type),
        beforeSend: function () { toggleLoading($btn, true); }
      })
      .done(function (res) {
        var error   = res && res.error;
        var message = res && res.message;
        var data    = res && res.data;

        if (error) {
          callTheme('showError', message || 'Fehler beim Entfernen des Gutscheins.', function (msg) {
            if (win.alert) alert(msg);
          });
          return;
        }

        callTheme('showSuccess', message || 'Gutschein entfernt.');
        var state = setCheckoutState(data, ctx.type);
        if (!state.coupon || !state.coupon.code) {
          setStoredCouponState(ctx, false);
        }
        reloadPaymentList(ctx.type);

        // Event für andere Module
        $(document).trigger('coupon.removed', {
          context: ctx.type,
          response: res,
        });
      })
      .fail(handleRequestError)
      .always(function () {
        toggleLoading($btn, false);
        activeCouponRequest = null;
      });
    });

})(window, window.jQuery);
