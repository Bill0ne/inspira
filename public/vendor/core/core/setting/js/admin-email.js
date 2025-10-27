/******/ (() => { // webpackBootstrap
/*!***********************************************************!*\
  !*** ./platform/core/setting/resources/js/admin-email.js ***!
  \***********************************************************/
$(function () {
  var $wrapper = $('[data-bb-toggle="admin-email"]');
  if (!$wrapper.length) {
    return;
  }
  var $addBtn = $wrapper.find('#add');
  var max = parseInt($wrapper.data('max'), 10);
  var emails = $wrapper.data('emails');
  if (emails.length === 0) {
    emails = [''];
  }
  var onAddEmail = function onAddEmail() {
    var count = $wrapper.find('input[type=email]').length;
    if (count >= max) {
      $addBtn.addClass('disabled');
    } else {
      $addBtn.removeClass('disabled');
    }
  };
  var addEmail = function addEmail() {
    var value = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
    return $wrapper.find('label').after("<div class=\"d-flex mt-2 more-email align-items-center\">\n                <input type=\"email\" class=\"form-control\" placeholder=\"".concat($addBtn.data('placeholder'), "\" name=\"admin_email[]\" value=\"").concat(value ? value : '', "\" />\n                <a class=\"btn btn-link btn-sm text-danger bg-transparent border-0\"><svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon icon-tabler icon-tabler-minus\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\">\n  <path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"/>\n  <path d=\"M5 12l14 0\" />\n</svg>\n</a>\n            </div>"));
  };
  var render = function render() {
    emails.forEach(function (email) {
      addEmail(email);
    });
    onAddEmail();
  };
  $wrapper.on('click', '.more-email > a', function () {
    if ($(this).hasClass('disabled')) {
      return;
    }
    $(this).parent('.more-email').remove();
    onAddEmail();
  });
  $addBtn.on('click', function (e) {
    e.preventDefault();
    addEmail();
    onAddEmail();
  });
  render();
});
/******/ })()
;