/**
 * checkout-hotel.js
 * Hotel-spezifische Recalc-Logik (Services, Foods, Slots) + Totals + Payment-Reload
 */
$(document).ready(function () {
  const $form = $('.payment-checkout-form');
  if (!$form.length) return;

  const collect = () => {
    const services = $('.service-item:checked').map((_, el) => $(el).val()).get();
    const foods    = $('.food-item:checked').map((_, el) => $(el).val()).get();
    const slots    = $('input[name^="slots["][name$="[start_date]"]').map((i, el) => ({
      start_date: $(el).val(),
      end_date:   $(`input[name="slots[${i}][end_date]"]`).val(),
    })).get();
    return { services, foods, slots };
  };

  function updateTotals(data){
    window.CheckoutCommerce?.updateTotals?.(data);
  }

  function recalc(){
    const payload = collect();
    const roomId  = $('input[name=room_id]').val();
    const $btn = $('.payment-checkout-btn').prop('disabled', true);

    $.get('/ajax/calculate-amount', { room_id: roomId, ...payload })
      .done(({ error, data, message }) => {
        if (error) return (window.RiorelaxTheme?.showError?.(message) ?? alert(message || 'Fehler bei der Berechnung.'));
        updateTotals(data);
        return window.CheckoutCommerce?.reloadPaymentList?.();
      })
      .always(() => $btn.prop('disabled', false));
  }

  $('.service-item, .food-item').on('change', recalc);
});
