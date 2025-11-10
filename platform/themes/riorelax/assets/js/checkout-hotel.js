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

    function recalc() {
      var payload = collect();
      var roomId  = $('input[name=room_id]').val();
      if (!roomId) return;

      var $btn = $('.payment-checkout-btn');
      if ($btn.length) $btn.prop('disabled', true);

      $.get('/ajax/calculate-amount', Object.assign({ room_id: roomId }, payload))
        .done(function (res) {
          var error   = res && res.error;
          var message = res && res.message;
          var data    = res && res.data;

          if (error) {
            win.RiorelaxTheme?.showError?.(message) ?? console.warn(message || 'Fehler bei der Berechnung.');
            return;
          }

          win.CheckoutCommerce?.updateTotals?.(data);
          return win.CheckoutCommerce?.reloadPaymentList?.();
        })
        .always(function () {
          if ($btn.length) $btn.prop('disabled', false);
        });
    }

    $(document).on('change', '.service-item, .food-item', recalc);
  });

})(window, window.jQuery);
