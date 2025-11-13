/* checkout-hotel.js
 * Hotel-spezifische Recalc-Logik (Services, Foods, Slots) + Totals + Payment-Reload
 * Greift auf CheckoutCommerce.updateTotals/reloadPaymentList zu.
 */
(function (win, $) {
  'use strict';
  if (!$) return;

  $(function () {
    var $form = $('.payment-checkout-form');
    if (!$form.length) return;

    var sharedApi = win.CheckoutCommerce || {};
    var isHotelCheckout = !!(sharedApi && sharedApi.isHotelCheckout);
    if (!isHotelCheckout) {
      isHotelCheckout = !!document.querySelector('input[name="room_id"]');
    }
    if (!isHotelCheckout) return;

    function collect() {
      var services = $('.service-item:checked').map(function (_, el) { return $(el).val(); }).get();
      var foods    = $('.food-item:checked').map(function (_, el) { return $(el).val(); }).get();
      var slots    = $('input[name^="slots["][name$="[start_date]"]').map(function (i, el) {
        return {
          start_date: $(el).val(),
          end_date:   $('input[name="slots[' + i + '][end_date]"]').val()
        };
      }).get();
      return { services: services, foods: foods, slots: slots };
    }

    function updateTotals(data) {
      if (!data || typeof data !== 'object') return;
      if ('sub_total' in data) $('.amount-text').text(data.sub_total);
      if ('coupon_amount_formatted' in data) {
        var rawCoupon = parseFloat(data.coupon_amount_raw || 0);
        var $couponLine = $('.coupon-line');
        if ($couponLine.length) {
          if (rawCoupon > 0) $couponLine.removeClass('d-none');
          else $couponLine.addClass('d-none');
          $('.discount-text').text('-' + data.coupon_amount_formatted);
        }
      } else if ('discount_amount' in data) {
        $('.discount-text').text(data.discount_amount);
      }
      if ('mengenrabatt_amount' in data) {
        var rawQuantity = parseFloat(data.mengenrabatt_amount_raw || 0);
        var $quantityLine = $('.mengenrabatt-line');
        if ($quantityLine.length) {
          if (rawQuantity > 0) $quantityLine.removeClass('d-none');
          else $quantityLine.addClass('d-none');
          $('.quantity-discount-text').text((rawQuantity > 0 ? '-' : '') + data.mengenrabatt_amount);
        }
      }
      if ('tax_amount' in data) $('.tax-text').text(data.tax_amount);
      if ('total_amount' in data) $('.total-amount-text').text(data.total_amount);
      if ('amount_raw' in data) $('input[name=amount]').val(data.amount_raw);
    }

    function reloadPaymentList() {
      var $list = $('.payment-checkout-form .list_payment_method');
      if (!$list.length) return $.Deferred().resolve();

      var selected = $list.find('input[name="payment_method"]:checked').val();
      var dfd = $.Deferred();

      $list.load(window.location.href + ' .payment-checkout-form .list_payment_method > *', function (resp, status) {
        if (status === 'error') {
          dfd.reject();
          return;
        }
        if (selected) {
          $list.find('input[name="payment_method"][value="' + selected + '"]').prop('checked', true).trigger('change');
        }
        dfd.resolve();
      });

      return dfd.promise();
    }

    var applyTotals = typeof sharedApi.updateTotals === 'function' ? sharedApi.updateTotals : updateTotals;
    var refreshPayments = typeof sharedApi.reloadPaymentList === 'function' ? sharedApi.reloadPaymentList : reloadPaymentList;

    function recalc() {
      var payload = collect();
      var roomId  = $('input[name=room_id]').val();
      if (!roomId) return;

      var $btn = $('.payment-checkout-btn');
      if ($btn.length) $btn.prop('disabled', true);

      $.get('/ajax/calculate-amount', $.extend({ room_id: roomId }, payload))
        .done(function (res) {
          var error   = res && res.error;
          var message = res && res.message;
          var data    = res && res.data;
          var theme   = win.RiorelaxTheme || {};

          if (error) {
            if (theme && typeof theme.showError === 'function') theme.showError(message);
            else if (win.console && console.warn) console.warn(message || 'Fehler bei der Berechnung.');
            return;
          }

          applyTotals(data, 'hotel');
          return refreshPayments('hotel');
        })
        .always(function () {
          if ($btn.length) $btn.prop('disabled', false);
        });
    }

    $(document).on('change', '.service-item, .food-item', recalc);
  });

})(window, window.jQuery);
