/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/*!****************************************************!*\
  !*** ./platform/core/base/resources/js/cleanup.js ***!
  \****************************************************/


$(function () {
  $(document).on('click', '.btn-trigger-cleanup', function (event) {
    event.preventDefault();
    $('#cleanup-modal').modal('show');
  });
  $(document).on('click', '#cleanup-submit-action', function (event) {
    event.preventDefault();
    event.stopPropagation();
    var _self = $(event.currentTarget);
    Botble.showButtonLoading(_self);
    var $form = $('#form-cleanup-database');
    var $modal = $('#cleanup-modal');
    $httpClient.make().post($form.prop('action'), new FormData($form[0])).then(function (_ref) {
      var data = _ref.data;
      return Botble.showSuccess(data.message);
    })["finally"](function () {
      Botble.hideButtonLoading(_self);
      $modal.modal('hide');
    });
  });
});
/******/ })()
;