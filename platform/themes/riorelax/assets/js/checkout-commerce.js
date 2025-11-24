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

  function updateTotals(data, context) {
    if (!data || typeof data !== 'object') return;
    var ctx = getActiveContext(context);
    if (!ctx) return;
    var root = getContextRoot(ctx) || document;
    var $root = $(root);
    // Auch 0-Werte übernehmen (deshalb 'in' statt truthy)
    if ('sub_total'       in data) $root.find('.amount-text').text(data.sub_total);
    if ('discount_amount' in data) $root.find('.discount-text').text(data.discount_amount);
    if ('tax_amount'      in data) $root.find('.tax-text').text(data.tax_amount);
    if ('total_amount'    in data) $root.find('.total-amount-text').text(data.total_amount);
    if ('amount_raw'      in data) $root.find('input[name=amount]').val(data.amount_raw);
  }

  function getSharedPayload(context) {
    var payload = {};
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
      dfd.resolve();
    });

    return dfd.promise();
  }

  // Expose für andere Module (Hotel-Recalc ruft das auf)
  var checkoutApi = {
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
        updateTotals(data, ctx.type);
        if (data && typeof data.coupon_code !== 'undefined') {
          ctx.$box.find('input[name=coupon_hidden]').val(data.coupon_code || '');
          if (data.coupon_code) {
            ctx.$box.find('input[name=coupon_code]').val(data.coupon_code);
            setStoredCouponState(ctx, true);
          }
        }
        refreshCouponBox(data && data.coupon_view, ctx.type);
        reloadPaymentList(ctx.type);

        // Event für andere Module (z.B. course-checkout.js)
        $(document).trigger('coupon.applied', {
          context: ctx.type,
          response: res,
        });
      })
      .fail(function (err) {
        callTheme('handleError', err, function () {
          if (win.console && console.error) console.error(err);
        });
      })
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
        updateTotals(data, ctx.type);
        if (data && typeof data.coupon_code !== 'undefined') {
          ctx.$box.find('input[name=coupon_hidden]').val('');
          if (!data.coupon_code) {
            ctx.$box.find('input[name=coupon_code]').val('');
            setStoredCouponState(ctx, false);
          }
        }
        refreshCouponBox(data && data.coupon_view, ctx.type);
        reloadPaymentList(ctx.type);

        // Event für andere Module
        $(document).trigger('coupon.removed', {
          context: ctx.type,
          response: res,
        });
      })
      .fail(function (err) {
        callTheme('handleError', err, function () {
          if (win.console && console.error) console.error(err);
        });
      })
      .always(function () {
        toggleLoading($btn, false);
        activeCouponRequest = null;
      });
    });

})(window, window.jQuery);
