/* checkout-commerce.js
 * Gemeinsames Commerce: Coupon apply/remove + Payment-Liste reload
 * Robust gegen 0-Werte (nutzt 'in' Checks), sichert CSRF und Fehlerfälle ab.
 */
(function (win, $) {
  'use strict';
  if (!$) return;

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

  function updateTotals(data) {
    if (!data || typeof data !== 'object') return;
    // Auch 0-Werte übernehmen (deshalb 'in' statt truthy)
    if ('sub_total'      in data) $('.amount-text').text(data.sub_total);
    if ('discount_amount'in data) $('.discount-text').text(data.discount_amount);
    if ('tax_amount'     in data) $('.tax-text').text(data.tax_amount);
    if ('total_amount'   in data) $('.total-amount-text').text(data.total_amount);
    if ('amount_raw'     in data) $('input[name=amount]').val(data.amount_raw);
  }

  function getSharedPayload() {
    var payload = {};
    var courseInput = document.querySelector('input[name="course_id"]');

    if (courseInput && courseInput.value) {
      payload.course_id = courseInput.value;
    }

    return payload;
  }

  function refreshCouponBox(html) {
    var $container = $('#couponBox');

    if (!$container.length) {
      $container = $('.coupon-wrapper').first();
    }

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
          refreshCouponBox(res.data);
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

  function reloadPaymentList() {
    var $list = $('.payment-checkout-form .list_payment_method');
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
    reloadPaymentList: reloadPaymentList
  };

  $(document)
    .on('click', '.toggle-coupon-form', function () {
      $('.coupon-form').toggle('fast');
    })
    .on('click', '.apply-coupon-code', function (e) {
      e.preventDefault();

      var $btn = $(e.currentTarget);
      var url  = $btn.data('url');
      var code = ($('input[name=coupon_code]').val() || '').trim();

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
        data: $.extend({ coupon_code: code }, getSharedPayload()),
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
        refreshCouponBox(data && data.coupon_view);
        reloadPaymentList();
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
      var url  = $btn.data('url');
      if (!url) return;

      $.ajax({
        url: url,
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': getCsrf() },
        data: getSharedPayload(),
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
        refreshCouponBox(data && data.coupon_view);
        reloadPaymentList();
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
