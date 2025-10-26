/******/ (() => { // webpackBootstrap
/*!*******************************************************************!*\
  !*** ./platform/packages/get-started/resources/js/get-started.js ***!
  \*******************************************************************/
window.getStartedCookie = function () {
  var COOKIE_VALUE = 1;
  var COOKIE_NAME = 'get_started_cookie';
  var COOKIE_DOMAIN = window.location.hostname;
  function setupCookie() {
    var expirationInDays = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 1;
    setCookie(COOKIE_NAME, COOKIE_VALUE, expirationInDays);
  }
  function cookieExists() {
    return document.cookie.split('; ').indexOf(COOKIE_NAME + '=' + COOKIE_VALUE) !== -1;
  }
  function setCookie(name, value, expirationInDays) {
    var date = new Date();
    date.setTime(date.getTime() + expirationInDays * 24 * 60 * 60 * 1000);
    document.cookie = "".concat(name, "=").concat(value, ";expires=").concat(date.toUTCString(), ";domain=").concat(COOKIE_DOMAIN, ";path=/");
  }
  return {
    setupCookie: setupCookie,
    cookieExists: cookieExists
  };
}();
$(function () {
  if (!window.getStartedCookie.cookieExists()) {
    $('.get-started-modal[data-step="1"]').modal('show');
  }
  $(document).on('click', '.get-started-modal button[type=submit]', function (event) {
    event.preventDefault();
    event.stopPropagation();
    var _self = $(this);
    var _form = _self.closest('form');
    Botble.showButtonLoading(_self);
    $httpClient.make().postForm(_form.prop('action'), new FormData(_form[0])).then(function (_ref) {
      var data = _ref.data;
      _self.closest('.get-started-modal').modal('hide');
      $(".get-started-modal[data-step=\"".concat(data.data.step, "\"]")).modal('show');
    })["finally"](function () {
      Botble.hideButtonLoading(_self);
    });
  });
  $('.get-started-modal .btn-close').on('click', function (event) {
    event.preventDefault();
    var step = $(this).closest('.get-started-modal').data('step');
    $('.js-back-to-wizard').data('step', step);
    $(this).closest('.get-started-modal').modal('hide');
    $('.close-get-started-modal').modal('show');
  });
  $(document).on('click', '.js-back-to-wizard', function (event) {
    event.preventDefault();
    $(this).closest('.close-get-started-modal').modal('hide');
    $(".get-started-modal[data-step=\"".concat($(this).data('step'), "\"]")).modal('show');
  });
  $(document).on('click', '.js-close-wizard', function (event) {
    event.preventDefault();
    window.getStartedCookie.setupCookie();
    $(this).closest('.close-get-started-modal').modal('hide');
  });
  $(document).on('click', '.resume-setup-wizard', function (event) {
    event.preventDefault();
    $('.get-started-modal[data-step="1"]').modal('show');
  });
  $('.get-started-modal').on('hide.bs.modal', function () {
    if (!window.getStartedCookie.cookieExists()) {
      window.getStartedCookie.setupCookie(0.2);
    }
  });
});
/******/ })()
;