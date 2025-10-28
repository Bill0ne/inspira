/******/ (() => { // webpackBootstrap
/*!*****************************************************!*\
  !*** ./platform/core/setting/resources/js/email.js ***!
  \*****************************************************/
$(function () {
  $('[data-bb-toggle="test-email-send"]').on('click', function (event) {
    event.preventDefault();
    var _self = $(event.currentTarget);
    var form = new FormData(_self.closest('form')[0]);
    Botble.showButtonLoading(_self);
    $httpClient.make().postForm(_self.data('url'), form).then(function (_ref) {
      var data = _ref.data;
      Botble.showSuccess(data.message);
      $('#send-test-email-modal').modal('show');
    })["finally"](function () {
      Botble.hideButtonLoading(_self);
    });
  });
  $('#send-test-email-btn').on('click', function (event) {
    event.preventDefault();
    var _self = $(event.currentTarget);
    Botble.showButtonLoading(_self);
    $httpClient.make().post(_self.data('url'), {
      email: _self.closest('.modal-content').find('input[name=email]').val(),
      template: _self.closest('.modal-content').find('select[name=template]').val()
    }).then(function (_ref2) {
      var data = _ref2.data;
      Botble.showSuccess(data.message);
      _self.closest('.modal').modal('hide');
    })["finally"](function () {
      Botble.hideButtonLoading(_self);
    });
  });
});
/******/ })()
;