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

  function updateTotals(data) {
    if (!data || typeof data !== 'object') return;
    // Auch 0-Werte übernehmen (deshalb 'in' statt truthy)
    if ('sub_total'      in data) $('.amount-text').text(data.sub_total);
    if ('discount_amount'in data) $('.discount-text').text(data.discount_amount);
    if ('tax_amount'     in data) $('.tax-text').text(data.tax_amount);
    if ('total_amount'   in data) $('.total-amount-text').text(data.total_amount);
    if ('amount_raw'     in data) $('input[name=amount]').val(data.amount_raw);
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
      if (!code.length) return (win.RiorelaxTheme?.showError?.('Bitte Gutscheincode eingeben.') ?? alert('Bitte Gutscheincode eingeben.'));

      $.ajax({
        url: url,
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': getCsrf() },
        data: { coupon_code: code }
      })
      .done(function (res) {
        var error   = res && res.error;
        var message = res && res.message;
        var data    = res && res.data;

        if (error) return (win.RiorelaxTheme?.showError?.(message) ?? alert(message || 'Fehler beim Anwenden des Gutscheins.'));

        win.RiorelaxTheme?.showSuccess?.(message || 'Gutschein angewendet.');
        updateTotals(data);
        reloadPaymentList();
      })
      .fail(function (err) {
        win.RiorelaxTheme?.handleError?.(err) ?? console.error(err);
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
        headers: { 'X-CSRF-TOKEN': getCsrf() }
      })
      .done(function (res) {
        var error   = res && res.error;
        var message = res && res.message;
        var data    = res && res.data;

        if (error) return (win.RiorelaxTheme?.showError?.(message) ?? alert(message || 'Fehler beim Entfernen des Gutscheins.'));

        win.RiorelaxTheme?.showSuccess?.(message || 'Gutschein entfernt.');
        updateTotals(data);
        reloadPaymentList();
      })
      .fail(function (err) {
        win.RiorelaxTheme?.handleError?.(err) ?? console.error(err);
      });
    });

})(window, window.jQuery);
