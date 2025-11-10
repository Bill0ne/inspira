/**
 * checkout-commerce.js
 * Gemeinsames Commerce: Coupon apply/remove + Payment-Methoden reload
 */
(function(){
  const $doc = $(document);

  function updateTotals(data){
    if (!data) return;
    if (data.sub_total)        $('.amount-text').text(data.sub_total);
    if (data.discount_amount)  $('.discount-text').text(data.discount_amount);
    if (data.tax_amount)       $('.tax-text').text(data.tax_amount);
    if (data.total_amount)     $('.total-amount-text').text(data.total_amount);
    if (data.amount_raw != null) $('input[name=amount]').val(data.amount_raw);
  }

  function reloadPaymentList(){
    const $list = $('.payment-checkout-form .list_payment_method');
    if (!$list.length) return $.Deferred().resolve();
    const selected = $list.find('input[name="payment_method"]:checked').val();

    const dfd = $.Deferred();
    $list.load(window.location.href + ' .payment-checkout-form .list_payment_method > *', function(resp, status){
      if (status === 'error') return dfd.reject();
      if (selected) {
        $list.find('input[name="payment_method"][value="'+selected+'"]').prop('checked', true).trigger('change');
      }
      dfd.resolve();
    });
    return dfd.promise();
  }

  // Expose für andere Module (Hotel-Recalc ruft das ggf. auf)
  window.CheckoutCommerce = {
    updateTotals,
    reloadPaymentList
  };

  $doc
    .on('click', '.toggle-coupon-form', () => $('.coupon-form').toggle('fast'))
    .on('click', '.apply-coupon-code', (e) => {
      e.preventDefault();
      const $btn = $(e.currentTarget);
      const url  = $btn.data('url');
      const code = ($('input[name=coupon_code]').val() || '').trim();

      if (!url) return;
      if (!code.length) return (window.RiorelaxTheme?.showError?.('Bitte Gutscheincode eingeben.') ?? alert('Bitte Gutscheincode eingeben.'));

      $.ajax({
        url: url,
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: { coupon_code: code }
      })
      .done(({ error, message, data }) => {
        if (error) return (window.RiorelaxTheme?.showError?.(message) ?? alert(message || 'Fehler beim Anwenden des Gutscheins.'));
        window.RiorelaxTheme?.showSuccess?.(message || 'Gutschein angewendet.');
        updateTotals(data);
        reloadPaymentList();
      })
      .fail(err => window.RiorelaxTheme?.handleError?.(err) ?? console.error(err));
    })
    .on('click', '.remove-coupon-code', (e) => {
      e.preventDefault();
      const $btn = $(e.currentTarget);
      const url  = $btn.data('url');
      if (!url) return;

      $.ajax({
        url: url,
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      })
      .done(({ error, message, data }) => {
        if (error) return (window.RiorelaxTheme?.showError?.(message) ?? alert(message || 'Fehler beim Entfernen des Gutscheins.'));
        window.RiorelaxTheme?.showSuccess?.(message || 'Gutschein entfernt.');
        updateTotals(data);
        reloadPaymentList();
      })
      .fail(err => window.RiorelaxTheme?.handleError?.(err) ?? console.error(err));
    });
})();
