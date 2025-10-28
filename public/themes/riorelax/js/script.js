/******/ (() => { // webpackBootstrap
/*!******************************************************!*\
  !*** ./platform/themes/riorelax/assets/js/script.js ***!
  \******************************************************/
var RiorelaxTheme = RiorelaxTheme || {};
window.RiorelaxTheme = RiorelaxTheme;
toastr.options = {
  positionClass: 'toast-bottom-right'
};
RiorelaxTheme.showError = function (message) {
  toastr.error(message);
};
RiorelaxTheme.showSuccess = function (message) {
  toastr.success(message);
};
RiorelaxTheme.isRtl = function () {
  return document.body.dir === 'rtl';
};
RiorelaxTheme.handleError = function (data) {
  if (typeof data.errors !== 'undefined' && data.errors.length) {
    RiorelaxTheme.handleValidationError(data.errors);
  } else if (typeof data.responseJSON !== 'undefined') {
    if (typeof data.responseJSON.errors !== 'undefined') {
      if (data.status === 422) {
        RiorelaxTheme.handleValidationError(data.responseJSON.errors);
      }
    } else if (typeof data.responseJSON.message !== 'undefined') {
      RiorelaxTheme.showError(data.responseJSON.message);
    } else {
      $.each(data.responseJSON, function (index, el) {
        $.each(el, function (key, item) {
          RiorelaxTheme.showError(item);
        });
      });
    }
  } else {
    RiorelaxTheme.showError(data.statusText);
  }
};
RiorelaxTheme.handleValidationError = function (errors) {
  var message = '';
  $.each(errors, function (index, item) {
    if (message !== '') {
      message += '<br />';
    }
    message += item;
  });
  RiorelaxTheme.showError(message);
};
(function ($) {
  'use strict';

  $(document).on('submit', '.newsletter-form', function (event) {
    event.preventDefault();
    event.stopPropagation();
    var _self = $(event.target);
    var _btn = _self.find('button[type="submit"]');
    $.ajax({
      type: 'POST',
      cache: false,
      url: _self.closest('form').prop('action'),
      data: new FormData(_self.closest('form')[0]),
      contentType: false,
      processData: false,
      beforeSend: function beforeSend() {
        _btn.addClass('button-loading');
        _btn.attr('disable');
      },
      success: function success(res) {
        if (!res.error) {
          _self.closest('form').find('input[type=email]').val('');
          RiorelaxTheme.showSuccess(res.message);
        } else {
          RiorelaxTheme.handleError(res.message);
        }
      },
      error: function error(res) {
        RiorelaxTheme.handleError(res);
      },
      complete: function complete() {
        if (typeof refreshRecaptcha !== 'undefined') {
          refreshRecaptcha();
        }
        _btn.removeClass('button-loading');
        _btn.removeAttr('disable');
      }
    });
  });
  initDatePicker();
})(jQuery);
function initDatePicker() {
  if ($('.form-booking .date-picker').length > 0) {
    var date = new Date();
    var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    $('.form-booking .date-picker').each(function () {
      var options = {
        autoclose: true,
        startDate: today
      };
      if ($(this).data('locale')) {
        options.language = $(this).data('locale');
      }
      if ($(this).data('date-format')) {
        options.format = $(this).data('date-format');
      }
      $(this).datepicker(options);
    });
  }
}
/******/ })()
;