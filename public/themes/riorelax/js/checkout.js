/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/*!********************************************************!*\
  !*** ./platform/themes/riorelax/assets/js/checkout.js ***!
  \********************************************************/


$(document).ready(function () {
  $('.service-item').on('change', function () {
    var foods = [];
    var services = [];
    $('.service-item:checked').each(function (i, el) {
      services[i] = $(el).val();
    });
    var slots = [];
    $('input[name^="slots["][name$="[start_date]"]').each(function (i, el) {
      var start = $(el).val();
      var end = $(document).find("input[name=\"slots[".concat(i, "][end_date]\"]")).val();
      if (start && end) {
        slots.push({
          start_date: start,
          end_date: end
        });
      }
    });
    $('.food-item:checked').each(function (i, el) {
      foods[i] = $(el).val();
    });
    $('body').css('cursor', 'progress');
    $('.custom-checkbox label').css('cursor', 'progress');
    var $checkoutButton = $(document).find('.payment-checkout-btn');
    $checkoutButton.prop('disabled', true);
    var $selectedPaymentMethod = $(document).find('.payment-checkout-form .list_payment_method input[name="payment_method"]:checked').val();
    $.ajax({
      type: 'GET',
      cache: false,
      url: '/ajax/calculate-amount',
      data: {
        room_id: $('input[name=room_id]').val(),
        slots: slots,
        services: services,
        foods: foods
      },
      success: function success(_ref) {
        var error = _ref.error,
          data = _ref.data;
        if (!error) {
          $('.total-amount-text').text(data.total_amount);
          $('input[name=amount]').val(data.amount_raw);
          $('.amount-text').text(data.sub_total);
          $('.discount-text').text(data.discount_amount);
          $('.tax-text').text(data.tax_amount);
        }
        $('body').css('cursor', 'default');
        $('.custom-checkbox label').css('cursor', 'pointer');
        $('.payment-checkout-form .list_payment_method').load(window.location.href + ' .payment-checkout-form .list_payment_method > *', function () {
          $checkoutButton.prop('disabled', false);
          $(document).find('.payment-checkout-form .list_payment_method input[value="' + $selectedPaymentMethod + '"]').prop('checked', true).trigger('change');
        });
      },
      error: function error() {
        $('body').css('cursor', 'default');
        $('.custom-checkbox label').css('cursor', 'pointer');
      }
    });
  });
  $('.food-item').on('change', function () {
    var foods = [];
    var services = [];
    $('.food-item:checked').each(function (i, el) {
      foods[i] = $(el).val();
    });
    var slots = [];
    $('input[name^="slots["][name$="[start_date]"]').each(function (i, el) {
      var start = $(el).val();
      var end = $(document).find("input[name=\"slots[".concat(i, "][end_date]\"]")).val();
      if (start && end) {
        slots.push({
          start_date: start,
          end_date: end
        });
      }
    });
    $('.service-item:checked').each(function (i, el) {
      services[i] = $(el).val();
    });
    $('body').css('cursor', 'progress');
    $('.custom-checkbox label').css('cursor', 'progress');
    var $checkoutButton = $(document).find('.payment-checkout-btn');
    $checkoutButton.prop('disabled', true);
    var $selectedPaymentMethod = $(document).find('.payment-checkout-form .list_payment_method input[name="payment_method"]:checked').val();
    $.ajax({
      type: 'GET',
      cache: false,
      url: '/ajax/calculate-amount',
      data: {
        room_id: $('input[name=room_id]').val(),
        slots: slots,
        foods: foods,
        services: services
      },
      success: function success(_ref2) {
        var error = _ref2.error,
          data = _ref2.data;
        if (!error) {
          $('.total-amount-text').text(data.total_amount);
          $('input[name=amount]').val(data.amount_raw);
          $('.amount-text').text(data.sub_total);
          $('.discount-text').text(data.discount_amount);
          $('.tax-text').text(data.tax_amount);
        }
        $('body').css('cursor', 'default');
        $('.custom-checkbox label').css('cursor', 'pointer');
        $('.payment-checkout-form .list_payment_method').load(window.location.href + ' .payment-checkout-form .list_payment_method > *', function () {
          $checkoutButton.prop('disabled', false);
          $(document).find('.payment-checkout-form .list_payment_method input[value="' + $selectedPaymentMethod + '"]').prop('checked', true).trigger('change');
        });
      },
      error: function error() {
        $('body').css('cursor', 'default');
        $('.custom-checkbox label').css('cursor', 'pointer');
      }
    });
  });
  $('.create-customer').on('change', 'input[name="register_customer"]', function (event) {
    var $formCreate = $('.form-create-customer-password');
    if (event.target.checked) {
      $formCreate.removeClass('d-none');
    } else {
      $formCreate.addClass('d-none');
    }
  });
  var refreshCoupon = function refreshCoupon() {
    var services = [];
    $('.service-item:checked').each(function (i, el) {
      services[i] = $(el).val();
    });
    var slots = [];
    $('input[name^="slots["][name$="[start_date]"]').each(function (i, el) {
      var start = $(el).val();
      var end = $(document).find("input[name=\"slots[".concat(i, "][end_date]\"]")).val();
      if (start && end) {
        slots.push({
          start_date: start,
          end_date: end
        });
      }
    });
    var $checkoutButton = $(document).find('.payment-checkout-btn');
    $checkoutButton.prop('disabled', true);
    var $selectedPaymentMethod = $(document).find('.payment-checkout-form .list_payment_method input[name="payment_method"]:checked').val();
    $.ajax({
      url: '/ajax/calculate-amount',
      type: 'GET',
      data: {
        room_id: $('input[name=room_id]').val(),
        slots: slots,
        services: services
      },
      success: function success(_ref3) {
        var _$$val;
        var error = _ref3.error,
          message = _ref3.message,
          data = _ref3.data;
        if (error) {
          RiorelaxTheme.showError(message);
          return;
        }
        $('.total-amount-text').text(data.total_amount);
        $('input[name=amount]').val(data.amount_raw);
        $('.amount-text').text(data.sub_total);
        $('.discount-text').text(data.discount_amount);
        $('.tax-text').text(data.tax_amount);
        $('.payment-checkout-form .list_payment_method').load(window.location.href + ' .payment-checkout-form .list_payment_method > *', function () {
          $checkoutButton.prop('disabled', false);
          $(document).find('.payment-checkout-form .list_payment_method input[value="' + $selectedPaymentMethod + '"]').prop('checked', true).trigger('change');
        });
        var refreshUrl = $('.order-detail-box').data('refresh-url');
        $.ajax({
          url: refreshUrl,
          type: 'GET',
          data: {
            coupon_code: (_$$val = $('input[name=coupon_hidden]').val()) !== null && _$$val !== void 0 ? _$$val : $('input[name=coupon_code]').val()
          },
          success: function success(_ref4) {
            var error = _ref4.error,
              message = _ref4.message,
              data = _ref4.data;
            if (error) {
              RiorelaxTheme.showError(message);
              return;
            }
            $('.order-detail-box').html(data);
          },
          error: function error(_error) {
            RiorelaxTheme.handleError(_error);
          }
        });
      },
      error: function error(_error2) {
        RiorelaxTheme.handleError(_error2);
      }
    });
  };
  $(document).on('click', '.toggle-coupon-form', function () {
    return $(document).find('.coupon-form').toggle('fast');
  }).on('click', '.apply-coupon-code', function (e) {
    e.preventDefault();
    var slots = [];
    $('input[name^="slots["][name$="[start_date]"]').each(function (i, el) {
      var start = $(el).val();
      var end = $(document).find("input[name=\"slots[".concat(i, "][end_date]\"]")).val();
      if (start && end) {
        slots.push({
          start_date: start,
          end_date: end
        });
      }
    });
    var $button = $(e.currentTarget);
    $.ajax({
      url: $button.data('url'),
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        coupon_code: $('input[name=coupon_code]').val()
      },
      beforeSend: function beforeSend() {
        $button.addClass('button-loading');
      },
      success: function success(_ref5) {
        var error = _ref5.error,
          message = _ref5.message;
        if (error) {
          RiorelaxTheme.showError(message);
          return;
        }
        RiorelaxTheme.showSuccess(message);
        refreshCoupon();
      },
      error: function error(_error3) {
        RiorelaxTheme.handleError(_error3);
      },
      complete: function complete() {
        $button.removeClass('button-loading');
      }
    });
  }).on('click', '.remove-coupon-code', function (e) {
    e.preventDefault();
    var $button = $(e.currentTarget);
    $.ajax({
      url: $button.data('url'),
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      beforeSend: function beforeSend() {
        $button.addClass('button-loading');
      },
      success: function success(_ref6) {
        var message = _ref6.message,
          error = _ref6.error;
        if (error) {
          RiorelaxTheme.showError(message);
          return;
        }
        RiorelaxTheme.showSuccess(message);
        refreshCoupon();
      },
      error: function error(_error4) {
        RiorelaxTheme.handleError(_error4);
      },
      complete: function complete() {
        $button.removeClass('button-loading');
      }
    });
  });
});
/******/ })()
;