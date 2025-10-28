/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/*!***************************************************************!*\
  !*** ./platform/themes/riorelax/assets/js/course-checkout.js ***!
  \***************************************************************/


$(document).ready(function () {
  // Function to refresh booking amounts and coupon box
  var refreshCourseCoupon = function refreshCourseCoupon() {
    var $checkoutButton = $('.payment-checkout-btn');
    $checkoutButton.prop('disabled', true);
    var $selectedPaymentMethod = $('.payment-checkout-form .list_payment_method input[name="payment_method"]:checked').val();

    // Get coupon code from hidden input or user input
    var couponCode = $('input[name=coupon_hidden]').val() || $('input[name=coupon_code]').val();

    // Calculate booking amount via AJAX
    $.ajax({
      url: '/course/ajax/calculate-amount',
      type: 'GET',
      data: {
        course_id: $('input[name=course_id]').val(),
        coupon_code: couponCode
      },
      success: function success(_ref) {
        var error = _ref.error,
          message = _ref.message,
          data = _ref.data;
        if (error) {
          RiorelaxTheme.showError(message);
          return;
        }

        // Update sidebar totals
        $('.total-amount-text').text(data.total_amount);
        $('input[name=amount]').val(data.amount_raw);
        $('.amount-text').text(data.sub_total);
        $('.discount-text').text(data.discount_amount);
        $('.tax-text').text(data.tax_amount);

        // Reload payment methods (preserve selection)
        $('.payment-checkout-form .list_payment_method').load(window.location.href + ' .payment-checkout-form .list_payment_method > *', function () {
          $checkoutButton.prop('disabled', false);
          $('.payment-checkout-form .list_payment_method input[value="' + $selectedPaymentMethod + '"]').prop('checked', true).trigger('change');
        });

        // Refresh order detail box (coupon info)
        var refreshUrl = $('.order-detail-box').data('refresh-url');
        $.ajax({
          url: refreshUrl,
          type: 'GET',
          data: {
            coupon_code: couponCode
          },
          success: function success(_ref2) {
            var error = _ref2.error,
              message = _ref2.message,
              data = _ref2.data;
            if (!error) {
              $('.order-detail-box').html(data);
            } else {
              RiorelaxTheme.showError(message);
            }
          },
          error: function error(err) {
            return RiorelaxTheme.handleError(err);
          }
        });
      },
      error: function error(err) {
        return RiorelaxTheme.handleError(err);
      }
    });
  };

  // Toggle coupon form
  $(document).on('click', '.toggle-coupon-form', function () {
    return $('.coupon-form').toggle('fast');
  })

  // Apply coupon
  .on('click', '.apply-coupon-code', function (e) {
    e.preventDefault();
    var $button = $(e.currentTarget);
    var couponCode = $('input[name=coupon_code]').val();
    if (!couponCode) {
      RiorelaxTheme.showError('Please enter a coupon code.');
      return;
    }
    $.ajax({
      url: $button.data('url'),
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        coupon_code: couponCode
      },
      beforeSend: function beforeSend() {
        return $button.addClass('button-loading');
      },
      success: function success(_ref3) {
        var error = _ref3.error,
          message = _ref3.message;
        if (error) {
          RiorelaxTheme.showError(message);
          return;
        }
        RiorelaxTheme.showSuccess(message);
        refreshCourseCoupon();
      },
      error: function error(err) {
        return RiorelaxTheme.handleError(err);
      },
      complete: function complete() {
        return $button.removeClass('button-loading');
      }
    });
  })

  // Remove coupon
  .on('click', '.remove-coupon-code', function (e) {
    e.preventDefault();
    var $button = $(e.currentTarget);
    $.ajax({
      url: $button.data('url'),
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      beforeSend: function beforeSend() {
        return $button.addClass('button-loading');
      },
      success: function success(_ref4) {
        var error = _ref4.error,
          message = _ref4.message;
        if (error) {
          RiorelaxTheme.showError(message);
          return;
        }
        RiorelaxTheme.showSuccess(message);
        refreshCourseCoupon();
      },
      error: function error(err) {
        return RiorelaxTheme.handleError(err);
      },
      complete: function complete() {
        return $button.removeClass('button-loading');
      }
    });
  });
});
/******/ })()
;