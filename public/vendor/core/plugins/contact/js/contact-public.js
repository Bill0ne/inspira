/******/ (() => { // webpackBootstrap
/*!*****************************************************************!*\
  !*** ./platform/plugins/contact/resources/js/contact-public.js ***!
  \*****************************************************************/
$(function () {
  var showError = function showError(message) {
    $('.contact-error-message').html(message).show();
  };
  var showSuccess = function showSuccess(message) {
    $('.contact-success-message').html(message).show();
  };
  var handleError = function handleError(data) {
    if (typeof data.errors !== 'undefined' && data.errors.length) {
      handleValidationError(data.errors);
    } else {
      if (typeof data.responseJSON !== 'undefined') {
        if (typeof data.responseJSON.errors !== 'undefined') {
          if (data.status === 422) {
            handleValidationError(data.responseJSON.errors);
          }
        } else if (typeof data.responseJSON.message !== 'undefined') {
          showError(data.responseJSON.message);
        } else {
          $.each(data.responseJSON, function (index, el) {
            $.each(el, function (key, item) {
              showError(item);
            });
          });
        }
      } else {
        showError(data.statusText);
      }
    }
  };
  var handleValidationError = function handleValidationError(errors) {
    var message = '';
    $.each(errors, function (index, item) {
      if (message !== '') {
        message += '<br />';
      }
      message += item;
    });
    showError(message);
  };
  $(document).on('submit', '.contact-form', function (event) {
    event.preventDefault();
    event.stopPropagation();
    var $form = $(this);
    var $button = $form.find('button[type=submit]');
    $('.contact-success-message').html('').hide();
    $('.contact-error-message').html('').hide();
    $.ajax({
      type: 'POST',
      cache: false,
      url: $form.prop('action'),
      data: new FormData($form[0]),
      contentType: false,
      processData: false,
      beforeSend: function beforeSend() {
        return $button.addClass('button-loading');
      },
      success: function success(_ref) {
        var error = _ref.error,
          message = _ref.message;
        if (!error) {
          $form[0].reset();
          showSuccess(message);
        } else {
          showError(message);
        }
        if (typeof refreshRecaptcha !== 'undefined') {
          refreshRecaptcha();
        }
        document.dispatchEvent(new CustomEvent('contact-form.submitted'));
      },
      error: function error(_error) {
        if (typeof refreshRecaptcha !== 'undefined') {
          refreshRecaptcha();
        }
        handleError(_error);
      },
      complete: function complete() {
        return $button.removeClass('button-loading');
      }
    });
  });
});
/******/ })()
;