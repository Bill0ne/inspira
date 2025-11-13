/* checkout-commerce.js
 * Gemeinsames Commerce: Coupon apply/remove + Payment-Liste reload
 * Robust gegen 0-Werte (nutzt 'in' Checks), sichert CSRF und Fehlerfälle ab.
 */
(function (win, $) {
  'use strict';
  if (!$) return;

  var contextRoots = {
    course: document.querySelector('[data-checkout-context="course"]'),
    hotel: document.querySelector('[data-checkout-context="hotel"]')
  };

  function hasContext(context) {
    if (!contextRoots[context]) return false;
    if (context === 'course') {
      return !!document.querySelector('input[name="course_id"]');
    }
    if (context === 'hotel') {
      return !!document.querySelector('input[name="room_id"]');
    }
    return false;
  }

  var isCourseCheckout = hasContext('course');
  var isHotelCheckout = hasContext('hotel');

  function getActiveContext(fallback) {
    if (fallback && hasContext(fallback)) return fallback;
    if (isCourseCheckout) return 'course';
    if (isHotelCheckout) return 'hotel';
    return null;
  }

  function resolveContext(element) {
    var el = element && element.nodeType ? element : null;
    while (el) {
      if (el.hasAttribute('data-checkout-context')) {
        var type = el.getAttribute('data-checkout-context');
        if (type && hasContext(type)) {
          var root = contextRoots[type];
          if (!root) {
            return null;
          }
          return {
            type: type,
            root: root,
            $root: $(root),
            $box: $(type === 'course' ? root.querySelector('#courseCouponBox') : root.querySelector('#hotelCouponBox'))
          };
        }
      }
      el = el.parentElement;
    }
    return null;
  }

  function ensureContextRoot(context) {
    var ctx = getActiveContext(context);
    return ctx ? contextRoots[ctx] : null;
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
    var root = contextRoots[ctx] || document;
    var $root = $(root);
    // Auch 0-Werte übernehmen (deshalb 'in' statt truthy)
    if ('sub_total'      in data) $root.find('.amount-text').text(data.sub_total);
    if ('discount_amount'in data) $root.find('.discount-text').text(data.discount_amount);
    if ('tax_amount'     in data) $root.find('.tax-text').text(data.tax_amount);
    if ('total_amount'   in data) $root.find('.total-amount-text').text(data.total_amount);
    if ('amount_raw'     in data) $root.find('input[name=amount]').val(data.amount_raw);
  }

  function getSharedPayload(context) {
    var payload = {};
    if (context === 'course' && isCourseCheckout) {
      var courseInput = document.querySelector('input[name="course_id"]');
      if (courseInput && courseInput.value) {
        payload.course_id = courseInput.value;
      }
    }
    if (context === 'hotel' && isHotelCheckout) {
      var roomInput = document.querySelector('input[name="room_id"]');
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

    if (typeof html === 'string') {
      $container.html(html);
      var $form = $container.find('.coupon-form');
      if ($form.length) {
        var hasApplied = $container.find('.coupon-feedback').length > 0;
        if (hasApplied) {
          $form.show();
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
    var root = contextRoots[ctx] || document;
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
  win.CheckoutCommerce = {
    updateTotals: updateTotals,
    reloadPaymentList: reloadPaymentList,
    isCourseCheckout: isCourseCheckout,
    isHotelCheckout: isHotelCheckout
  };

  $(document)
    .on('click', '.toggle-coupon-form', function (e) {
      var ctx = resolveContext(e.target);
      if (!ctx) return;
      if (ctx.type === 'course' && !isCourseCheckout) return;
      if (ctx.type === 'hotel' && !isHotelCheckout) return;
      ctx.$box.find('.coupon-form').toggle('fast');
    })
    .on('click', '.apply-coupon-code', function (e) {
      e.preventDefault();

      var $btn = $(e.currentTarget);
      var ctx = resolveContext(e.currentTarget);
      if (!ctx) return;
      if (ctx.type === 'course' && !isCourseCheckout) return;
      if (ctx.type === 'hotel' && !isHotelCheckout) return;
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

      $.ajax({
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
        updateTotals(data);
        if (data && typeof data.coupon_code !== 'undefined') {
          ctx.$box.find('input[name=coupon_hidden]').val(data.coupon_code || '');
          if (data.coupon_code) {
            ctx.$box.find('input[name=coupon_code]').val(data.coupon_code);
          }
        }
        refreshCouponBox(data && data.coupon_view, ctx.type);
        reloadPaymentList(ctx.type);
      })
      .fail(function (err) {
        callTheme('handleError', err, function () {
          if (win.console && console.error) console.error(err);
        });
      })
      .always(function () {
        toggleLoading($btn, false);
      });
    })
    .on('click', '.remove-coupon-code', function (e) {
      e.preventDefault();

      var $btn = $(e.currentTarget);
      var ctx = resolveContext(e.currentTarget);
      if (!ctx) return;
      if (ctx.type === 'course' && !isCourseCheckout) return;
      if (ctx.type === 'hotel' && !isHotelCheckout) return;
      var url  = $btn.data('url');
      if (!url) return;

      $.ajax({
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
        updateTotals(data);
        if (data && typeof data.coupon_code !== 'undefined') {
          ctx.$box.find('input[name=coupon_hidden]').val('');
          if (!data.coupon_code) {
            ctx.$box.find('input[name=coupon_code]').val('');
          }
        }
        refreshCouponBox(data && data.coupon_view, ctx.type);
        reloadPaymentList(ctx.type);
      })
      .fail(function (err) {
        callTheme('handleError', err, function () {
          if (win.console && console.error) console.error(err);
        });
      })
      .always(function () {
        toggleLoading($btn, false);
      });
    });

})(window, window.jQuery);
