/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/*!***************************************************************!*\
  !*** ./platform/core/base/resources/js/license-activation.js ***!
  \***************************************************************/


$(function () {
  $(document).on('submit', '[data-bb-toggle="activate-license"]', function (e) {
    e.preventDefault();
    var $form = $(this);
    var formData = new FormData(e.currentTarget);
    Botble.showLoading($form[0]);
    $httpClient.make().postForm($form.prop('action'), formData).then(function (_ref) {
      var data = _ref.data;
      Botble.showSuccess(data.message);
      if ($form.data('reload')) {
        setTimeout(function () {
          window.location.reload();
        }, 1000);
        return;
      }
      var redirect = $form.data('redirect');
      if (redirect) {
        window.location.assign(redirect);
      }
    })["finally"](function () {
      Botble.hideLoading($form[0]);
    });
  });
});
/******/ })()
;